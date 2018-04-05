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
 * Date: 9/1/2017
 * Time: 9:26 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;


class ListCommand extends AbstractCommand
{

    protected function getFields() {
        $config = $this->httpRequest->getNodeConfig();
        if (!array_key_exists('fields', $config)) {
            return '*';
        }
        $fields = implode(', ', $config['fields']);

        return $fields;
    }


    protected function isCustomQuery() {
        return $this->getFields() != '*';
    }

//$queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getMasterBucketName() .
//"` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
//$this->getFilter($params) . ' LIMIT 1';


    public function execute($params = array(), $request = array()) {

        $connection = $this->getConnection();
        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getBucketName($this->entity) .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . $this->getOrderBy($params, 'id') .
            $this->getLimit($params);

        $query = \CouchbaseN1qlQuery::fromString($queryString);
        $rows = $connection->getBucket()->query($query);


        return array(
            $this->entity->getIdentityField() => $this->removeRowHeadings($this->resultsToArray($rows, false, $this->isCustomQuery())),
            $this->entity->getIdentityField() . 'Count' => $this->getTotalRowCount($params)
        );

    }
    
    

    public function getTotalRowCount($params = array(), $request = array()) {

        $queryString = "SELECT count('id') as rowCount FROM `" . $this->getBucketName($this->entity) .
            "` WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params);

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        //$rows = $this->getConnection()->getBucket($this->entity)->query($query);
        $rows = $this->getConnection()->getBucket()->query($query);

        return $this->resultsToArray($rows, true);
    }


    protected function getOrderBy(array &$params, $column = null) {
        $orderBy = (is_null($column) ? '' : ' ORDER BY ' . $column );


        if (array_key_exists('directive::ORDER_BY', $params)) {
            $column = $params['directive::ORDER_BY'];

            $orderBy = ' ORDER BY ' . $column;
            unset($params['directive::ORDER_BY']);
        }
        if (array_key_exists('directive::DIRECTION', $params)) {
            $orderBy .= ' ' . $params['directive::DIRECTION'];
            unset($params['directive::DIRECTION']);
        } else{
            $orderBy .= ' ASC';
        }

        return $orderBy;
    }


    protected function getLimit(array &$params) {
        $limit = '';
        $offset = '';

        if (array_key_exists('directive::OFFSET', $params)) {
            $offset = ' OFFSET ' . intval($params['directive::OFFSET']);
            unset($params['directive::OFFSET']);
        }

        if (array_key_exists('directive::LIMIT', $params)) {
            $limit = ' LIMIT ' . intval($params['directive::LIMIT']);
            unset($params['directive::LIMIT']);
        }

        return $limit . $offset;
    }
}