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
 * Time: 8:37 PM
 */

namespace Gossamer\Core\MVC;


use Gossamer\Core\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use exceptions\ParameterNotPassedException;

use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Nephthys\Rest\GenericRestClient;
use Gossamer\Set\Utils\Container;
use Monolog\Logger;
use exceptions\HandlerNotCallableException;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Core\Views\AJAXExceptionView;
use Gossamer\Core\Views\JSONExceptionView;
use Validation\Exceptions\ValidationFailedException;

abstract class AbstractComponent
{

    use \Gossamer\Set\Utils\ContainerTrait;

    private $controllerName = null;
    private $modelName = null;
    private $method = null;
    private $params = null;
    private $logger = null;
    private $viewName;
    private $agentType;


    /**
     *
     * @param string $controllerName
     * @param string $viewName
     * @param string $modelName
     * @param string $method
     * @param array $params
     * @param Logger $logger
     * @param array $agentType
     *
     * @throws ParameterNotPassedException
     */
    public function __construct($controllerName, $viewName, $modelName, $method = null, array $params = null, LoggingInterface $logger, array $agentType) {

        if (is_null($controllerName)) {
            throw new KeyNotSetException('controller name is null');
        } else if (is_null($modelName)) {
            throw new KeyNotSetException('model is null');
        }
        $this->controllerName = $controllerName;

        $this->viewName = $viewName;

        $this->modelName = $modelName;

        $this->method = $method;

        $this->params = $params;

        $this->logger = $logger;

        $this->agentType = $agentType;
    }


    /**
     * @param HttpRequest $httpRequest
     * @param HttpResponse $httpResponse
     * @return mixed
     * @throws HandlerNotCallableException
     */
    public function handleRequest(HttpRequest &$httpRequest, HttpResponse &$httpResponse) {
        $handler = array(
            $this->controllerName,
            $this->method
        );

        //if it throws an exception we are catching it in the calling Kernel
        if (is_callable($handler)) {
            $model = new $this->modelName($httpRequest, $httpResponse, $this->logger);
            $model->setContainer($this->container);

            $view = new $this->viewName($this->logger, $httpRequest->getRequestParams()->getYmlKey(), $this->agentType, $httpRequest, $httpResponse);
            $view->setContainer($this->container);

            $controller = new $this->controllerName($model, $view, $this->logger, $httpRequest, $httpResponse);
            $controller->setContainer($this->container);

            if (array_key_exists('Gossamer\Core\Components\security\traits\GetLoggedInMemberTrait', class_uses($controller))) {
                $controller->setHttpRequest($httpRequest);
            }
            
                return call_user_func_array(array(
                    $controller,
                    $this->method
                ), is_null($httpRequest->getRequestParams()->getUriParameters()) ? array() : $httpRequest->getRequestParams()->getUriParameters());


        } else {
            pr($handler);
            throw new HandlerNotCallableException('unable to match method ' . $this->method . ' to controller with key: ' . $httpRequest->getRequestParams()->getYmlKey());
        }

    }


    protected function getChildNamespace() {
        return substr(get_called_class(), 0, strrpos(get_called_class(), '\\'));
    }
}