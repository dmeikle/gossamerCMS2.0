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
 * Date: 3/19/2017
 * Time: 12:24 AM
 */

namespace Gossamer\Core\CommandFactory;


use Gossamer\Core\CommandFactory\Exceptions\HandlerNotCallableException;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;

/**
 * Class AbstractComponent
 * @package Gossamer\Core\CommandFactory
 *
 * This class is intended for the DBKernel to use. We only have one type of view (JSON Response) and this
 * Kernel is only intended to handle requests to the database - not a lot of requirements for an MVC
 * framework on this aspect. So here we use the Command Factory pattern - determine the request type and
 * instantiate the appropriate command for it.
 */
class AbstractComponent
{

    use \Gossamer\Set\Utils\ContainerTrait;
    use \Gossamer\Pesedget\Entities\EntityManagerTrait;

    protected $nodeConfig;

    protected $documentName;

    protected $commandName;

    protected $logger;



    public function __construct(array $nodeConfig, $documentName, $commandName, LoggingInterface $logger) {
        $this->nodeConfig = $nodeConfig;
        $this->documentName = $documentName;
        $this->commandName = $commandName;
        $this->logger = $logger;
    }

    /**
     * @param HttpRequest $httpRequest
     * @param HttpResponse $httpResponse
     * @return mixed
     * @throws HandlerNotCallableException
     */
    public function handleRequest(HttpRequest &$httpRequest, HttpResponse &$httpResponse) {
        $handler = array(
            $this->commandName,
            'execute'
        );

        //if it throws an exception we are catching it in the calling Kernel
        if (is_callable($handler)) {

            $document = new $this->documentName();
            $command = new $this->commandName($document,  $this->logger, $httpRequest, $httpResponse, $this->container->get('EntityManager'));
            $command->setContainer($this->container);

            return $command->execute($httpRequest->getRequestParams()->getQueryStringParameters(), $httpRequest->getRequestParams()->getPost());
        } else {
            error_log(print_r($handler, true));
            throw new HandlerNotCallableException('unable to match method execute() to command with key: ' . $httpRequest->getRequestParams()->getYmlKey());
        }
    }

}