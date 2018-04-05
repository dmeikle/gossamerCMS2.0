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
 * Date: 9/4/2017
 * Time: 9:52 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Traits;


use components\shoppingcart\documents\CartProduct;
use Gossamer\Essentials\Configuration\YamlLoader;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\DocumentFactory;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;

trait BucketTrait
{
    protected $entity;

    protected $entityManager;

    protected $httpResponse;

    protected $httpRequest;

    protected function getEntityManager() {
        if (is_null($this->entityManager)) {
            return $this->container->get('EntityManager');
        }

        return $this->entityManager;
    }


    protected function getConnection($key = null) {

        if (!is_null($key)) {
            return $this->entityManager->getConnection($key);
        }

        return $this->entityManager->getConnection($this->httpRequest->getNodeConfig()['datasource']);
    }


    protected function getDocumentConnection(Document $document) {

        $path = $this->getEntityPath($document);
        $config = null;
        try{
            $config = $this->getEntityConfig($document, $path)['bucket'];
        }catch(\Exception $e) {
            //ok - it's not overridden - so return the default connection instead
            $defaultConnection = $this->getEntityManager()->getDefaultConnection();

            //$config = $defaultConnection->getCredential('dbName');
            return $defaultConnection;
        }

        return $this->entityManager->getConnection($config);
    }


    protected function getEntityConfig(Document $document, $filepath) {
        $factory = new DocumentFactory(new YamlLoader());

        return $factory->getEntityConfig($document, $filepath);
    }

    private function getNodeConfig() {
        if(!is_null($this->httpRequest)) {
            return $this->httpRequest->getNodeConfig();
        }
    }

    protected function getBucket($masterBucket = false) {

        $nodeConfig = $this->getNodeConfig();

        if ($masterBucket === true) {

            return $this->container->get('EntityManager')->getConnection()->getBucket($this->getMasterBucketName());
        }
        if ($masterBucket === false) {

            return $this->container->get('EntityManager')->getConnection()->getBucket($this->getBucketName());
        }

        if ($masterBucket instanceof Document) {
            $path = $this->getEntityPath($masterBucket);
            $config = null;

            try{
                $config = $this->getEntityConfig($masterBucket, $path)['bucket'];
            }catch(\Exception $e) {
                //ok - it's not overridden - so return the default connection instead
                $defaultConnection = $this->getEntityManager()->getDefaultConnection();

                $config = $defaultConnection->getCredentials();
            }

            $bucketName = '';
            if (array_key_exists('bucket', $config)) {
                $bucketName = $config['bucket'];
            }elseif (array_key_exists('dbName', $config)) {
                $bucketName = $config['dbName'];
            } elseif(is_array($config)) {
                $bucketName = $config['entity_db'];
            }else{
                $bucketName = $config;
            }

            return $this->container->get('EntityManager')->getConnection()->getBucket($bucketName);
        }
        //they've passed a bucketName - request it for them
        return $this->container->get('EntityManager')->getConnection()->getBucket($masterBucket);

    }

    protected function getEntityPath(Document $document) {
        $namepace = explode('\\', $document->getNamespace());
        $directory = array_shift($namepace);

        return $this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $directory .
        DIRECTORY_SEPARATOR . array_shift($namepace) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'entities.yml';
    }

    protected function getBucketName(Document $document = null) {

        $connName = '';
        if(is_null($document)){
            $config = $this->httpRequest->getAttribute('CLIENT_SERVER_DB_CONFIG');

            if (!is_null($config)) {
                return $config['bucketName'];
            }

            $connName = $this->httpRequest->getAttribute('NODE_LEVEL_CLIENT_DATABASE');
            return $this->container->get('EntityManager')->getConnection($connName)->getCredential('dbName');
        } else {

            $path = $this->getEntityPath($document);

            try{
                return $this->getEntityConfig($document, $path)['bucket'];
            }catch(\Exception $e) {
                //ok - it's not overridden - so return the default connection instead
                $defaultConnection = $this->getEntityManager()->getDefaultConnection();

                return $defaultConnection->getCredential('dbName');
            }

        }


    }


    protected function getMasterBucketName() {

        $config = $this->httpRequest->getAttribute('CLIENT_SERVER_DB_CONFIG');

        if (!is_null($config)) {
            return $config['masterBucketName'];
        }

        $conn = $this->container->get('EntityManager')->getDefaultConnection();

        return $conn->getCredential('dbName');
    }


    protected function getSchema(Document $document, $filepath) {
        $loader = new YamlLoader();
        $loader->setFilepath($filepath);
        $config = $loader->loadConfig();

        if (!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if (!array_key_exists($document->getIdentityField(), $config)) {
            throw new KeyNotFoundException($document->getIdentityField() . ' not found in configuration');
        }

        return $config[$document->getIdentityField()];
    }


    protected function resultsToArray($results, $shiftArray = false, $customQuery = false) {

        if (!is_object($results)) {
            return array();
        }
        if ($shiftArray) {

            if (isset($results->rows)) {
                $result = current(json_decode(json_encode($results->rows), TRUE));

                return $result;
            }

            return current(json_decode(json_encode($results->values), TRUE));
        }

        if (isset($results->rows)) {

            //pop it out of the array key

            $results = json_decode(json_encode($results->rows), TRUE);
            if(count($results) == 0) {
                //nothing left to do
                return $results;
            }
            reset($results);
            $key = key($results[0]);
            //not specifying '*' alters the resultset from couchbase... *slow golf claps...*
            if($customQuery) {
                return $results;
            }

            return array_column($results,$key); //json_decode(json_encode($results->rows), TRUE);
        }

        return json_decode(json_encode($results->value), TRUE);
    }


    protected function getFilter(array $params) {
        $retval = '';

        foreach ($params as $key => $value) {
            if ($key == 'locale' || strpos(strtolower($key), 'directive::') !== false) {
                continue;
            }
            if ($key == 'search') {
                return $this->getSearchFilter($value);
            } else {
                $filterType = $this->getFilterType();
                if($key != 'isActive' && $filterType !== false) {

                    $retval .= " AND (`" . str_replace('.','`.`',$key) . "` $filterType '$value%')";
                }else {
                    $retval .= " AND (`" . str_replace('.','`.`',$key) . "` = '$value')";
                }


            }
        }

        return $retval;
    }

    protected function getFilterType() {
        $nodeConfig = $this->httpRequest->getNodeConfig();
        if(array_key_exists('filter', $nodeConfig)) {
            return $nodeConfig['filter'];
        }

        return false;
    }

    protected function getSearchFilter($keyword) {
        $retval = '';

        foreach ($this->getSearchFields() as $field) {
            $retval .= " OR (`" . str_replace('.','`.`',$field) . "` LIKE '%$keyword%')";
        }
        if (strlen($retval) == 0) {
            return;
        }

        return 'AND (' . (substr($retval, 3)) . ')';
    }
  

    protected function removeRowHeadings($result) {
        if ($result === false) {
            return array();
        }

        return array_values($result);
    }

    protected function getSearchFields() {
        throw new \Exception('searchFields method must be overridden in calling class');
    }

    protected function executeQuery($queryString) {
        $connection = $this->getConnection();


        $query = \CouchbaseN1qlQuery::fromString($queryString);
        $rows = $connection->getBucket()->query($query);

        return $rows;
    }
}