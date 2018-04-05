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
 * Date: 4/9/2017
 * Time: 9:27 PM
 */

namespace Gossamer\Core\CommandFactory;



/**
 * Save Command Class
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
class GetCommand extends AbstractCommand {

    /**
     * retrieves a single row from the database
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($params = array(), $request = array()) {
        //$passableParams = $this->httpRequest->getParameters();
        //remove the uri
        // array_shift($passableParams);

        $this->getQueryBuilder()->where($params);
        $query = '';
        if ($this->entity instanceof AbstractI18nEntity) {
            $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ITEM_QUERY, QueryBuilder::PARENT_AND_CHILD);
        } else {
            $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ITEM_QUERY);
        }

        $firstResult = $this->query($query);
        $entityName = get_class($this->entity);

        if (is_array($firstResult) && count($firstResult) > 0) {

            $this->loadI18nValues($firstResult, $params);

            $this->loadChildTableParams($firstResult, $params);

            $this->loadOneToOneJoinTables($firstResult);

            $this->loadOneToManyJoinTables($firstResult, $params);


        }

        return array($entityName => is_array($firstResult) ? current($firstResult) : array());
    }

    private function getI18nValuesForPadding() {

        // file_put_contents('/var/www/shoppingcart/logs/test.log', 'padi18n: '.print_r($result,true)."\r\n", FILE_APPEND);
        $query = $this->getQueryBuilder()->getQuery(new Locale(), QueryBuilder::GET_ALL_ITEMS_QUERY);
        $locales = $this->query($query);

        return $locales;
    }

    /**
     * load child rows from I18n specific to requested entity
     */
    private function loadI18nValues(&$firstResult, $params) {
        if (!$this->entity instanceof AbstractI18nEntity) {

            return;
        }
        $filter = array($this->entity->getI18nIdentifier() => $firstResult[0]['id']);
        $localesKey = 'locales';

        if (is_array($params) && array_key_exists('locale', $params)) {
            $filter['locale'] = $params['locale'];
            $localesKey = $params['locale'];
        }

        $this->getQueryBuilder()->where($filter);
        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::CHILD_ONLY);

        $i18nResult = $this->query($query);
        //file_put_contents('/var/www/shoppingcart/logs/test.log', '$i18nResult: '.print_r($i18nResult,true)."\r\n", FILE_APPEND);

        if ($localesKey == 'locales') {
            $firstResult[0]['locales'] = $this->getLocaleKeys($i18nResult, $this->getI18nValuesForPadding());
        } else {
            $firstResult[0]['locales'][$localesKey] = $i18nResult;
        }
    }

    private function loadOneToOneJoinTables(&$firstResult) {

        if (!$this->entity instanceof OneToOneJoinInterface) {

            //file_put_contents('/var/www/shoppingcart/logs/test.log', "no one to many\r\n", FILE_APPEND);
            return;
        }

        $tables = $this->entity->getJoinRelationships();

        foreach ($tables as $objectName => $columns) {
            $object = new $objectName();
            $key = $object->getTableName() . '_id';

            if (array_key_exists($key, $firstResult[0])) {

                $filter = array('id' => $firstResult[0][$key]);
                $this->getQueryBuilder()->where($filter);
                $query = $this->getQueryBuilder()->getQuery($object, QueryBuilder::GET_ITEM_QUERY, QueryBuilder::CHILD_ONLY);

                $joinResult = $this->query($query);

                //do this to get the class name as a key
                $reflector = new \ReflectionClass($object);

                $firstResult[0][$reflector->getShortName()] = current($joinResult);
            }
        }
    }

    private function loadOneToManyJoinTables(&$firstResult, $params) {

        if (!$this->entity instanceof OneToManyJoinInterface) {

            return;
        }
        $tables = $this->entity->getManyJoinRelationships();

        foreach ($tables as $tablename => $table) {

            $reflector = new \ReflectionClass($tablename);

            $row = array($this->entity->getI18nIdentifier() => intval($firstResult[0]['id']));

            $this->getQueryBuilder()->where($row);
            $query = $this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::CHILD_ONLY);
            $this->query($query);

            $childResult = $this->query($this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::CHILD_ONLY));

            //grab any mocks that exist for pulling up sleeping dogs that we don't actually have legit entities for...
            $this->loadMockableRelationships($childResult, new $tablename());

            $firstResult[0][$reflector->getShortName()] = $childResult;
        }
    }

    private function loadChildTableParams(&$firstResult, $params) {

        if (!$this->entity instanceof OneToManyChildInterface) {
            //file_put_contents('/var/www/shoppingcart/logs/test.log', "no one to many\r\n", FILE_APPEND);
            return;
        }
        $tables = $this->entity->getChildRelationships();

        foreach ($tables as $tablename => $table) {

            $reflector = new \ReflectionClass($tablename);

            $row = array($this->entity->getI18nIdentifier() => intval($firstResult[0]['id']));

            $this->getQueryBuilder()->where($row);
            $query = $this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::CHILD_ONLY);
            $this->query($query);

            $childResult = $this->query($this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::CHILD_ONLY));

            //grab any mocks that exist for pulling up sleeping dogs that we don't actually have legit entities for...
            $this->loadMockableRelationships($childResult, new $tablename());

            $firstResult[0][$reflector->getShortName()] = $childResult;
        }
    }

    private function loadMockableRelationships(&$childResult, AbstractEntity $entity) {
        if (!$entity instanceof MockableRelationshipsInterface) {

            return;
        }

        //'ProductsI18n' => array('PurchaseItems.Products_id' => 'ProductsI18n.Products_id') 
        $mockRelationships = $entity->getMockableRelationships();
        $filter = '';
        foreach ($childResult as $key => $item) {
            foreach ($mockRelationships as $tablename => $rawFilter) {

                //slip the id in to join it to the parent table
                foreach ($rawFilter as $key2 => $value) {
                    $filter = ' and ' . $key2 . ' = ' . $item[$value];
                }

                $this->getQueryBuilder()->where($filter);
                //can't use current version of table builder, since it requires an entity object and some tables are child
                //entites which are completely virtual - need to do query manually for now.... or forever... muahahaha.
                $query = 'select * from ' . $tablename . ' where ' . substr($filter, 5);

                $result = $this->query($query);
                $item[$tablename] = $result;


                $childResult[$key] = $item;
            }
        }
    }

    private function getLocaleKeys(array $results, array $paddingLocales) {
        $retval = array();
        $existingLocales = array();
        $templateRow = array();
        foreach ($results as $row) {
            $retval[$row['locale']] = $row;
            //we need this for padding later
            $existingLocales[] = $row['locale'];
            if (count($templateRow) == 0) {
                $templateRow = $row;
                foreach ($templateRow as $key => $value) {
                    if (strpos($key, '_id') === false) {
                        $templateRow[$key] = ''; //empty the values, they're not needed
                    }
                }
            }
        }
        //now pad any new locales that have not yet been initialized with a value
        foreach ($paddingLocales as $pLocale) {
            if (!in_array($pLocale['locale'], $existingLocales)) {
                $retval[$pLocale['locale']] = $templateRow;
                //set the padded locale
                $retval[$pLocale['locale']]['locale'] = $pLocale['locale'];
            }
        }

        //file_put_contents('/var/www/shoppingcart/logs/test.log', "$retval" . print_r($retval, true), FILE_APPEND);

        return $retval;
    }

}