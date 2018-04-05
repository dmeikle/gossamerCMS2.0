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
 * Date: 4/6/2017
 * Time: 10:33 PM
 */

namespace Gossamer\Core\System;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Set\Utils\Container;

class KernelRunner implements KernelRunnerInterface
{
    private $httpRequest;

    private $httpResponse;
    
    private $container;
    
    public function __construct(HttpRequest &$httpRequest, HttpResponse &$httpResponse, Container $container) {
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->container = $container;
    }

    public function execute(array $nodeConfig) {
        
        $componentName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['component'];
        $controllerName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['controller'];
        $modelName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['model'];
        $viewName = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['view'];
        $method = $nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['method'];

        $component = new $componentName($controllerName, $viewName, $modelName, $method,
            $this->httpRequest->getRequestParams()->getRequestParameters(), $this->container->get('Logger'), $this->httpRequest->getRequestParams()->getLayoutType());
        $component->setContainer($this->container);

        return $component->handleRequest($this->httpRequest, $this->httpResponse);
    }
}