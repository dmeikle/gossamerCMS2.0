<?php


namespace Gossamer\Horus\EventListeners;

use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Monolog\Logger;
use Gossamer\Horus\Core\Request;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;

class EventHandler
{
    use \Gossamer\Caching\Traits\CacheManagerTrait;

    private $listeners = array();
    
    private $state = null;
    
    private $event = null;
    
    private $container = null;
    
    private $logger = null;
   
    private $request = null;

    private $response = null;
    
    private $datasourceFactory = null;
    

    private $datasources = null;
    
    private $datasourceKey = null;
    
    private $eventDispatcher = null;

    /**
     * EventHandler constructor.
     * @param Logger $logger
     * @param HttpRequest &$request
     * @param HttpInterface $response
     */
    public function __construct(Logger $logger, HttpRequest &$request, HttpResponse &$response) {
        
        $this->logger = $logger;
        $this->request = $request;
        $this->response = $response;        
    } 



    /**
     * accessor
     *
     * @param type $datasourceKey
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
     * @param EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcher &$eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $listener
     */
    public function setListener($listener) {
        
        $this->listeners[] = $listener;
        $this->logger->addDebug($listener['listener'] . ' added to listeners');
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
     * traverses list of listeners and executes their calls
     */
    public function notifyListeners() {

        foreach ($this->listeners as $listener) {

            $listenerClass = $listener['listener'];
           // echo $listenerClass.'::' . 'on_' . $this->state ."\r\n";
            $handler = array($listenerClass, 'on_' . $this->state);
            if(!class_exists($listenerClass)) {
                die($listenerClass . ' does not exist');
                throw new \Exception($listenerClass . ' does not exist');
            }

            if ($this->state == $listener['event'] && is_callable($handler)) {
                unset($listener['listener']);     
                
                $eventListener = new $listenerClass($this->logger, $this->request, $this->response);
                $eventListener->setDatasources($this->datasourceFactory, $this->datasources);
                $eventListener->setDatasourceKey($this->datasourceKey);
                $eventListener->setEventDispatcher($this->eventDispatcher);
                $eventListener->setConfig($listener);
                $eventListener->setCacheManager($this->cacheManager);
                $eventListener->setContainer($this->container);

                $eventListener->execute($this->state, $this->event);

            }
        }
      
     
    }


    /**
     * @param $state
     * @param Event $event
     * @throws \Exception
     */
    public function setState($state, Event &$event) {

        $this->state = $state;
        $this->event = $event;
        
        $this->notifyListeners();
    }
    
}
