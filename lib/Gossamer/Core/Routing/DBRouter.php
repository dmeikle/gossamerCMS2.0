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
 * Date: 3/5/2017
 * Time: 10:41 PM
 */

namespace Gossamer\Core\Routing;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Core\System\Exceptions\URINotFoundException;

class DBRouter extends Router
{
    protected function getRoutingFileName() {
        return 'db-routing.yml';
    }

    protected function getInitialRouting($requestURI) {

        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath()  . $this->getRoutingFileName());
        
        //if we haven't found anything matching see if we can simply return a default config
        if (!array_key_exists($requestURI, $config)) {
            if (!array_key_exists('default', $config)) {
                throw new URINotFoundException($requestURI . ' does not exist in YML configuration. Check request method type?');
            }

            return $config['default']['component'];
        }

        return $config[$requestURI]['component'];
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

        $key = array_search($httpRequest->getRequestParams()->getUri(), array_column($configList, 'pattern'));
        $configKeys = array_keys($configList);
        $nodeConfig = $configList[$configKeys[$key]];
        $nodeConfig['ymlKey'] = $configKeys[$key];

        return $nodeConfig;
    }

    /**
     *
     * load all yml files for this component and see if we need to merge configurations
     * based on yml key being located in each file
     *
     * @param array $nodeConfig
     */
    protected function loadAllNodeConfigurations(array &$nodeConfig) {
        $entities = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'entities.yml');
        $schemas = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'schemas.yml');
        $mappings = $this->loadConfig($this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'mappings.yml');

        if(array_key_exists(key($nodeConfig), $entities)) {
            $nodeConfig[key($nodeConfig)] = array_merge($entities[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }

        if(array_key_exists(key($nodeConfig), $schemas)) {
            $nodeConfig[key($nodeConfig)] = array_merge($schemas[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }
        if(array_key_exists(key($nodeConfig), $mappings)) {
            $nodeConfig[key($nodeConfig)] = array_merge($mappings[key($nodeConfig)], $nodeConfig[key($nodeConfig)]);
        }

    }
}