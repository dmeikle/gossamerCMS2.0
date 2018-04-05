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
 * Date: 3/1/2017
 * Time: 10:34 PM
 */

namespace Gossamer\Core\System;


use Gossamer\Horus\Filters\FilterEvents;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Set\Utils\Container;

class Kernel
{
    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
    
    private $container;

    private $logger;

    private $httpRequest;

    private $httpResponse;

    public function __construct(Container &$container, LoggingInterface $logger, HttpRequest &$httpRequest, HttpResponse &$httpResponse) {
        $this->container = $container;
        $this->logger = $logger;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    public function run() {
        //announce that we are now starting the request
        //fire off any events that are a associated with this event
        $event = new Event(KernelEvents::REQUEST_START);

        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::REQUEST_START, $event);

        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), KernelEvents::REQUEST_START, $event);

        //initialize the MVC
        $nodeConfig = $this->httpRequest->getNodeConfig();
     
        $cmd = $this->getKernelRunner();


        $result = $cmd->execute($nodeConfig);

        $this->httpResponse->setAttribute('result', $result['data']);

        //file_put_contents('/var/www/glenmeikle.com/logs/db-debug.log', print_r($this->httpRequest, true), FILE_APPEND);
       // echo "node filters\r\n";
        runFilters($this->httpRequest->getSiteParams()->getSitePath(). DIRECTORY_SEPARATOR . $this->httpRequest->getNodeConfig()['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'filters.yml', $this->httpRequest->getRequestParams()->getYmlKey(),FilterEvents::FILTER_REQUEST_FORWARD);
       // echo "all filters\r\n";
        runFilters($this->httpRequest->getSiteParams()->getConfigPath() . 'filters.yml', 'all',FilterEvents::FILTER_REQUEST_FORWARD);

        
        $event = new Event(KernelEvents::RESPONSE_END, $result);
        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::RESPONSE_END, $event);
        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), KernelEvents::RESPONSE_END, $event);

        /**
         * now we dump the response to the page
         */
        renderResult($result);
    }

    private function getKernelRunner() {
        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'config.yml');
        if (!array_key_exists('server_context', $config)) {
            throw new ConfigurationNotFoundException('server_context missing from application config');
        }

        if ($config['server_context'] === 'database') {

            return new DBKernelRunner($this->httpRequest, $this->httpResponse, $this->container);
        }
        
        return new KernelRunner($this->httpRequest, $this->httpResponse, $this->container);

    }

}