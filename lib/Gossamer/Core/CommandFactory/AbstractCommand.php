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
 * Time: 12:41 AM
 */

namespace Gossamer\Core\CommandFactory;


use Gossamer\Core\Http\HttpRequest;
use Gossamer\Core\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Entities\EntityManager;

abstract class AbstractCommand
{
    use \Gossamer\Set\Utils\ContainerTrait;

    protected $entity;

    protected $httpRequest;

    protected $httpResponse;

    protected $entityManager;

    protected $logger;

    public function __construct($entity, LoggingInterface $logger, HttpRequest &$httpRequest, HttpResponse &$httpResponse, EntityManager $entityManager) {
        $this->entity = $entity;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }



    protected function setError($message, $code = 600) {
        $this->httpRequest->setAttribute('result', array('success'=>'false', 'message'=>$message, 'code' => $code));
    }

    protected function setSuccess($message, $code = 200) {
        $result = $this->httpResponse->getAttributes();
        $result['success'] = 'true';
        $result['message'] = $message;
        $result['code'] = $code;

        return $result;
    }

    protected function query($query, $connName = null) {
        $nodeConfig = $this->httpRequest->getNodeConfig();
        $conn = $this->getConnection($nodeConfig['entity_db'], $connName);
        
        return $conn->query($query);
    }
    
    protected function getConnection($entityDB, $connName = null) {
        $conn = null;
        
        if(!is_null($connName)) {
            $conn = $this->entityManager->getConnection($connName);
        }else{
            $conn = $this->entityManager->getConnection($entityDB);
        }
        
        return $conn;
    }

    /**
     * executes code specific to the child class
     *
     * @param array     URI params
     * @param array     POST params
     */
    public abstract function execute($params = array(), $post = array());
}