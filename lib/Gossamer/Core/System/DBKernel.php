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


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Set\Utils\Container;

class DBKernel
{
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

        //initialize the CommandFactory
        $nodeConfig = $this->httpRequest->getNodeConfig();
        $componentName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['component'];
        $documentName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['document'];        
        $method = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['method'];

        $cmd = new $componentName($nodeConfig, $documentName, $method, $this->logger);
        $cmd->setContainer($this->container);

        $result = $cmd->handleRequest($this->httpRequest, $this->httpResponse);
        $event = new Event(KernelEvents::RESPONSE_END, $result);
        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::RESPONSE_END, $event);
        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), KernelEvents::RESPONSE_END, $event);

        /**
         * now we dump the response to the page
         */
        renderResult($result);
       
    }

}