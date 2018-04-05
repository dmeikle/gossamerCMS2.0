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
 * Time: 5:08 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;


use Gossamer\Essentials\Configuration\YamlLoader;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Database\DatastoreInterface;
use Gossamer\Pesedget\Database\GossamerDBConnection;
use Gossamer\Pesedget\Entities\EntityManager;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\DocumentFactory;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;
use Gossamer\Pesedget\Utils\YAMLParser;

abstract class AbstractCommand extends \Gossamer\Pesedget\Commands\AbstractCommand
{
    use \Gossamer\Pesedget\Extensions\Couchbase\Traits\BucketTrait;

    protected $bucketName;

    private $bucket = null;

    private $bucketList = array();



    public function __construct(DatastoreInterface $entity, LoggingInterface $logger, HttpRequest $httpRequest, HttpResponse $httpResponse, EntityManager $entityManager) {

        //$this->connection = $connection;
        $this->httpResponse = $httpResponse;
        $this->httpRequest = $httpRequest;
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    
}