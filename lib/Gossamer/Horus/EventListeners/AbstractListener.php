<?php

namespace Gossamer\Horus\EventListeners;

use Detection\MobileDetect;
use Gossamer\Caching\CacheManager;
use Gossamer\Horus\Http\HttpRequest; use Gossamer\Horus\Http\HttpResponse;
use Monolog\Logger;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;


class AbstractListener
{
    use \Gossamer\Horus\Traits\SessionTrait;
    
    protected $logger = null;
    
    protected $request = null;
    
    protected $response = null;
    
    protected $listenerConfig = null;
    
    protected $datasourceKey = null;
    
    protected $datasourceFactory = null;
    
    protected $container = null;

    protected $eventDispatcher = null;

    protected $cacheManager = null;

    const METHOD_DELETE = 'delete';
    const METHOD_SAVE = 'save';
    const METHOD_PUT = 'put';
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const VERB_LIST = 'list';
    const VERB_DELETE = 'delete';
    const VERB_GET = 'get';
    const VERB_SEARCH = 'search';
    const VERB_SAVE = 'save';
    const DIRECTIVES = 'directives';



    public function __construct(Logger &$logger, HttpRequest &$request, HttpResponse &$response) {
        $this->logger = $logger;
        $this->request = $request;
        $this->response = $response;
    }

    public function setCacheManager(CacheManager $cacheManager) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * accessor
     * @param string $datasourceKey
     */
    public function setDatasourceKey($datasourceKey) {
        $this->datasourceKey = $datasourceKey;
    }

    /**
     * accessor
     *
     * @param DatasourceFactory $factory
     * @param array $datasources
     */
    public function setDatasources(DatasourceFactory $factory, array $datasources) {
        $this->datasourceFactory = $factory;
        $this->datasources = $datasources;

    }

    /**
     * accessor
     *
     * @param Container $container
     */
    public function setContainer(Container &$container) {

        $this->container = $container;
    }


    /**
     * @return mixed
     */
    protected function getEntityManager() {
        return $this->container->get('EntityManager');
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcher &$eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @param array $config
     */
    public function setConfig(array $config) {
        $this->listenerConfig = $config;
    }



    /**
     * accessor
     *
     * @param type $modelName
     *
     * @return datasource
     */
    protected function getDatasource(ListenerAccessibleInterface $modelName) {

        /**
         * CP-251 - added multiple datasources to a single function. Need to check
         * for requested modelName before defaulting to datasourceKey which is
         * specified in default routing config.
         */
        if (is_object($modelName) && array_key_exists($modelName->getTablename(), $this->datasources)) {
            $datasource = $this->datasourceFactory->getDatasource($this->datasources[$modelName->getTablename()], $this->logger);
            $datasource->setDatasourceKey($this->datasources[$modelName->getTablename()]);
        } elseif (!is_object($modelName) && array_key_exists($modelName, $this->datasources)) {
            $datasource = $this->datasourceFactory->getDatasource($this->datasources[$modelName], $this->logger);
            $datasource->setDatasourceKey($this->datasources[$modelName]);
        } elseif (!is_null($this->datasourceKey)) {
            $datasource = $this->datasourceFactory->getDatasource($this->datasourceKey, $this->logger);
            $datasource->setDatasourceKey($this->datasourceKey);
        } else {
            throw new \Exception($modelName . ' datasource key missing from listeners configuration');
        }

        return $datasource;
    }



    /**
     * entry point. determines which on_ method to call based on configuration
     * and state.
     *
     * @param type $state - the occurrence - eg: request_start
     *                  method will call the on_request_start in the child class
     *
     * @param type $params - any values needed
     */
    public function execute($state, Event &$event) {

        $method = 'on_' . $state;

        if (method_exists($this, $method)) {
//            $reflector = new \ReflectionClass(get_class($this));
//            $parameters = $reflector->getMethod($method)->getParameters();
//            unset($reflector);

//            if(is_object($parameters[0]->getClass()) && $parameters[0]->getClass()->name == 'Gossamer\Horus\EventListeners\Event'){
//                //could be passed by reference if it's an Event...
//                call_user_func_array(array($this, $method), array(&$event));
//            } else {
//                $this->logger->addDebug('class: ' . get_class($this) . ' found');
//                call_user_func_array(array($this, $method), array($params));
//            }
            call_user_func_array(array($this, $method), array(&$event));

        }
    }

    /**
     *
     * @return Locale
     */
//    protected function getDefaultLocale() {
//        //check to see if it's in the query string - a menu request perhaps?
//        $queryLocale = $this->request->getQueryParameter('locale');
//        if (!is_null($queryLocale)) {
//            return array('locale' => $queryLocale);
//        }
//
//        $manager = new UserPreferencesManager($this->httpRequest);
//        $userPreferences = $manager->getPreferences();
//
//        if (!is_null($userPreferences) && $userPreferences instanceof UserPreferences) {
//            $locale = $userPreferences->getDefaultLocale();
//            if (strlen($locale) > 0) {
//                return array('locale' => $userPreferences->getDefaultLocale());
//            }
//        }
//
//        $config = $this->httpRequest->getAttribute('defaultPreferences');
//
//        return $config['default_locale'];
//    }

    /**
     * accessor
     *
     * @return SecurityToken
     */
    protected function getSecurityToken() {
        $serializedToken = $this->getSession('_security_secured_area');
        
        $token = unserialize($serializedToken);

        return $token;
    }



    /**
     * determines if we are dealing with a computer or mobile device
     *
     * @return array
     */
    protected function getLayoutType() {
        $detector = new MobileDetect();
        $isMobile = $detector->isMobile();
        $isTablet = $detector->isTablet();
        unset($detector);

        return array('isMobile' => $isMobile, 'isTablet' => $isTablet, 'isDesktop' => (!$isMobile && !$isTablet));
    }
}
