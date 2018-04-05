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
 * Date: 8/29/2017
 * Time: 5:00 PM
 */

namespace Gossamer\Pesedget\Commands;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Database\DatastoreInterface;
use Gossamer\Pesedget\Entities\EntityManager;

abstract class AbstractCommand
{
    use \Gossamer\Set\Utils\ContainerTrait;

    protected $connection;

    protected $entity;

    protected $entityManager;

    protected $httpRequest;

    protected $httpResponse;

    public function __construct(DatastoreInterface $entity, LoggingInterface $logger, HttpRequest $httpRequest, HttpResponse $httpResponse, EntityManager $entityManager) {

        //$this->connection = $connection;
        $this->httpResponse = $httpResponse;
        $this->httpRequest = $httpRequest;
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    protected function getConnection($key = null) {

        if (!is_null($key)) {
            return $this->entityManager->getConnection($key);
        }

        return $this->entityManager->getConnection($this->httpRequest->getNodeConfig()['entity_db']);
    }

    protected function getFields() {
        $config = $this->httpRequest->getNodeConfig();
        $fields = '*';
        
        if(array_key_exists('fields', $config)) {
            $fields = implode(', ', $config['fields']);
        }

        return $fields;
    }

    public abstract function execute($params = array(), $post = array());
}