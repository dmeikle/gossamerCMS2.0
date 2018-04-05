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
 * Time: 10:51 PM
 */

namespace Gossamer\Core\System;


use Gossamer\Core\Views\JSONView;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Set\Utils\Container;

class DBKernelRunner implements KernelRunnerInterface
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

        $nodePath = '';
        if(array_key_exists('componentFolder', $nodeConfig)) {
            $nodePath = $nodeConfig['componentFolder'] . '\\';
        }
        $componentName = $nodePath . $nodeConfig['component'];
        $commandName = $this->getCommand($nodeConfig);
        $documentName = $nodePath . 'documents\\' .$nodeConfig['document'];
       

        $component = new $componentName($nodeConfig, $documentName, $commandName, $this->container->get('Logger'));
        $component->setContainer($this->container);

        $result = $component->handleRequest($this->httpRequest, $this->httpResponse);

        $view = new JSONView(
            $this->container->get('Logger'),
            $this->httpRequest->getRequestParams()->getYmlKey(),
            $this->httpRequest->getRequestParams()->getLayoutType(),
            $this->httpRequest, $this->httpResponse
        );

        return $view->render($result);
    }

    private function getCommand(array $nodeConfig) {
        if(array_key_exists('method', $nodeConfig)) {
            return $nodeConfig['componentFolder'] . '\\commands\\' . $nodeConfig['method'] . 'Command';
        }
        $chunks = explode('/', $nodeConfig['pattern']);

        return $nodeConfig['componentFolder'] . '\\commands\\' . ucfirst($chunks[3]). 'Command';
    }

}