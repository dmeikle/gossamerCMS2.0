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
 * Date: 12/30/2017
 * Time: 3:19 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;


class SearchCommand extends ListCommand
{

    protected function getSearchFields() {
        $config = $this->httpRequest->getNodeConfig();

        if (!array_key_exists('searchFields', $config)) {
            throw new \Exception('No search fields defined in bootstrap');
        }

        return $config['searchFields'];
    }

    protected function isMatchAllFields() {
        $config = $this->httpRequest->getNodeConfig();

        if (!array_key_exists('matchAllFields', $config)) {
            throw new \Exception('No matchAllFields defined in bootstrap');
        }

        return $config['matchAllFields'] == 'true';
    }


    protected function getFilter(array $params) {

        $retval = '';
        $filterType = 'LIKE';
        $filterConcatenator = ($this->isMatchAllFields()) ? ' AND ' : ' OR  ';
        if (array_key_exists('precision', $params) && $params['precision'] == 'true') {
            $filterType = '=';
        }
        unset($params['precision']);
        if(array_key_exists('keywords', $params)) {
            $retval = $this->generateKeywordFilter($params,$filterType, $filterConcatenator);
        } else {
            $retval = $this->generateParamsFilter($params, $filterType, $filterConcatenator);
        }

        return ' AND (' . substr($retval, 5) . ')';
    }

    private function generateParamsFilter(array $params, $filterType, $filterConcatenator) {
        $retval = '';
        //the search is based on multiple passed parameters - perhaps an advanced search form
        //strip out any fields we aren't search by
        $params = array_intersect_key($params, array_flip($this->getSearchFields()));
        foreach ($params as $key => $value) {
            if($filterType == 'LIKE'){
                $retval .= " $filterConcatenator (`" . str_replace('.', '`.`', $key) . "` LIKE '%$value%')";
            }else{
                $retval .= " $filterConcatenator (`" . str_replace('.', '`.`', $key) . "` = '$value')";
            }
        }

        return $retval;
    }

    private function generateKeywordFilter(array $params, $filterType, $filterConcatenator) {
        $retval = '';
        //it's a universal search, check all fields for the single provided keyword
        $value = $params['keywords'];
        $searchfields = $this->getSearchFields();
        foreach($searchfields as $key) {
            if($filterType == 'LIKE'){
                $retval .= " $filterConcatenator (`" . str_replace('.', '`.`', $key) . "` LIKE '%$value%')";
            }else{
                $retval .= " $filterConcatenator (`" . str_replace('.', '`.`', $key) . "` = '$value')";
            }
        }

        return $retval;
    }


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
}