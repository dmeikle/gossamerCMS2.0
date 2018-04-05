<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/2/2017
 * Time: 10:20 PM
 */

namespace Gossamer\Core\Routing;


use Gossamer\Core\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Core\System\Exceptions\URINotFoundException;
use Gossamer\Caching\CacheManager;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;

class Router
{
    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
    use \Gossamer\Core\Routing\Traits\FilenameSanitizer;
    
    protected $logger = null;

    protected $httpRequest = null;

    public function __construct(LoggingInterface &$logger = null, HttpRequest &$httpRequest) {
        $this->logger = $logger;
        $this->httpRequest = $httpRequest;
    }

    protected function getRoutingFileName() {
        return 'routing.yml';
    }

    /**
     * @return bool
     * @throws ConfigurationNotFoundException
     * @throws KeyNotSetException
     * @throws URINotFoundException
     *
     * Entry point for getting the node configuration based on the uri
     */
    public function getCurrentNode() {

        $initialRoutingPath = $this->getInitialRouting($this->httpRequest->getRequestParams()->getUri());

        $componentConfig = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $initialRoutingPath);

        if (is_null($componentConfig)) {
            throw new ConfigurationNotFoundException();
        }

        $nodeConfig = $this->findConfigKeyByURIPattern($componentConfig, $this->httpRequest);

        if ($nodeConfig === false) {
            throw new KeyNotSetException('Cannot locate configuration for requested URL', 404);
        }

        //set the node path so we can access it for the remainder of the request
        $nodeConfig['componentPath'] = $this->trimFilename($initialRoutingPath);

        $this->loadAllNodeConfigurations($nodeConfig);
        $this->saveToCache($this->httpRequest->getRequestParams()->getUri(), $nodeConfig);

        $this->httpRequest->setNodeConfig($nodeConfig);

        return $nodeConfig;
    }

    private function trimFilename($path) {
        $tmp = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($tmp);
        array_pop($tmp);

        return implode(DIRECTORY_SEPARATOR, $tmp);
    }
    /**
     *
     * load all yml files for this component and see if we need to merge configurations
     * based on yml key being located in each file
     *
     * @param array $nodeConfig
     */
    protected function loadAllNodeConfigurations(array &$nodeConfig) {
        $listeners = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'listeners.yml');
        $parameters = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'parameters.yml');
        $view = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'views.yml');
        $encodings = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'encodings.yml');

        if(array_key_exists(key($nodeConfig), $listeners)) {
            $nodeConfig[key($nodeConfig)] = array_merge($listeners[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }

        if(array_key_exists(key($nodeConfig), $parameters)) {
            $nodeConfig[key($nodeConfig)] = array_merge($parameters[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }

        if(array_key_exists(key($nodeConfig), $view) && is_array($view[key($nodeConfig)])) {
            $nodeConfig[key($nodeConfig)] = array_merge($view[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }
    }


    /**
     * @param $uri
     * @param $config
     */
    private function saveToCache($uri, $config) {
        $cacheManager = new CacheManager();
        $cacheManager->setHttpRequest($this->httpRequest);

        $cacheManager->saveToCache('routing/' . $this->sanitizeFilename($uri), $config);
    }

    /**
     * Step 1
     *
     * checks the application routing file to see which component to reference
     *
     * @param $requestURI - the uri we are looking up
     * @return  - the uri we are looking up
     *
     * @throws KeyNotSetException
     * @throws URINotFoundException
     *
     */
    protected function getInitialRouting($requestURI) {

        $pieces = array_filter(explode('/', $requestURI));

        $chunk = array_shift($pieces);
        $slugs = $this->getRoutingSlugs();
        if (in_array($chunk, $slugs)) {
            $chunk = array_shift($pieces); //drop the admin for the routing file
        }

        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath()  . $this->getRoutingFileName());

        //if we haven't found anything matching see if we can simply return a default config
        if (!array_key_exists($chunk, $config)) {
            if (!array_key_exists('default', $config)) {
                throw new URINotFoundException($chunk . ' does not exist in YML configuration. Check request method type?');
            }

            return $config['default']['component'];
        }

        //return the first path
        foreach ($config[$chunk] as $key => $path) {
            return $path;
        }
    }

    /**
     * @param array $pieces
     * @return bool
     *
     * checks for an empty uri and figures we want to return to landing page
     */
    protected function checkIsHomePage(array $pieces) {
        if(count($pieces) == 0) {
            return true;
        }
    }

    //$config, 'pattern', '/users/list'
    /**
     * Step 2
     *
     * findConfigKeyByURIPattern    loads the configuration, determines the ymlKey based
     *                              on the pattern node in YML against the matching URI.
     *                              returns the ymlkey as well as all uri pieces
     *
     * @param array     configlist
     * @param string    the node we are searching for
     * @param string    the complete uri we are searching against
     */
    public function findConfigKeyByURIPattern($configList, HttpRequest &$httpRequest) {
        $cacheManager = new CacheManager();
        $cacheManager->setHttpRequest($this->httpRequest);
        $comparator = new URIComparator($cacheManager, $this->httpRequest);

        $uriConfig = $comparator->findPattern($configList,  $httpRequest->getRequestParams()->getUri());

        if(array_key_exists('parameters', $uriConfig)) {
            $httpRequest->getRequestParams()->setUriParameters($uriConfig['parameters']);
        }

        unset($comparator);
       // $this->setRequestUriParameters($uriConfig, $httpRequest);
        return $uriConfig;
    }

    private function setRequestUriParameters(array $uriConfig, HttpRequest $httpRequest) {
        
        $requestPieces = explode('/', $httpRequest->getRequestParams()->getUri());
        $uriPieces = explode('/', $uriConfig[key($uriConfig)]['pattern']);

        $tmp = array_diff( $requestPieces, $uriPieces);
    }

    private function getRoutingSlugs() {

        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'config.yml');

        if (!array_key_exists('routing_slugs', $config)) {
            throw new KeyNotSetException('routing_slugs not found in application config');
        }
        unset($parser);

        return $config['routing_slugs'];
    }
}