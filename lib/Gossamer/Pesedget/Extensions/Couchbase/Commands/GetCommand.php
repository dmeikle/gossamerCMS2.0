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
 * Time: 5:06 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;



class GetCommand extends AbstractCommand
{


    protected function isCustomQuery() {
        return $this->getFields() != '*';
    }


    public function execute($params = array(), $post = array()) {

        $connection = $this->getConnection();

        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getMasterBucketName() .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . ' LIMIT 1';

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $connection->getBucket()->query($query);
        if($this->isCustomQuery()) {
            return array($this->entity->getClassName()=> $this->resultsToArray($rows, true, $this->isCustomQuery()));
        }

        return array($this->entity->getClassName()=> $this->removeRowHeadings($this->resultsToArray($rows, true, $this->isCustomQuery())));
    }
}