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
 * Date: 4/7/2017
 * Time: 9:14 PM
 */

namespace Gossamer\Core\CommandFactory;

use Gossamer\Core\CommandFactory\AbstractCommand;

class ListCommand extends AbstractCommand
{


    /**
     * retrieves a multiple rows from the database
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($params = array(), $post = array()){

        if($this->entity instanceof OneToOneJoinInterface) {
            if(array_key_exists('locale', $params)) {
                $this->getQueryBuilder()->where($params['locale']);
            }

            $this->getQueryBuilder()->join($this->entity->getJoinRelationships());
        }

        $this->getQueryBuilder()->where($params);

        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::PARENT_AND_CHILD);

        $result = $this->query($query);
        $param = get_class($this->entity) . 's';

        return array($param => $result, $param . 'Count' => $this->getTotalRowCount());
    }


    protected function getTotalRowCount(array $params = null, $entity = null) {
        //'directive::LIMIT'

        $this->getQueryBuilder()->where($params);

        $query = $this->getQueryBuilder()->getQuery(is_null($entity) ? $this->entity : $entity, QueryBuilder::GET_COUNT_QUERY, QueryBuilder::PARENT_AND_CHILD);
        //file_put_contents('/var/www/shoppingcart/logs/test.log', '$param: '.$query."\r\n", FILE_APPEND);

        $result = $this->query($query);

        return $result;
    }


    protected function loadMockableRelationships(&$childResult, AbstractEntity $entity) {

        if(!$entity instanceof MockableRelationshipsInterface) {

            return;
        }

        //'ProductsI18n' => array('PurchaseItems.Products_id' => 'ProductsI18n.Products_id') 
        $mockRelationships = $entity->getMockableRelationships();


        $filter = '';
        foreach($childResult as $key => $item) {
            foreach($mockRelationships as $tablename => $rawFilter) {
                $fields = '*';
                //slip the id in to join it to the parent table
                foreach($rawFilter as $key2 => $value) {
                    if(is_array($value)) {
                        $fields = $value['fields'];
                        $column = $value['column'];
                        $filter = ' and ' . $key2 . ' = ' . $item[$column];
                    } else {
                        $filter = ' and ' . $key2 . ' = ' . $item[$value];
                    }
                }

                $this->getQueryBuilder()->where($filter);
                //can't use current version of table builder, since it requires an entity object and some tables are child
                //entites which are completely virtual - need to do query manually for now.... or forever... muahahaha.
                $query = 'select ' . $fields . ' from ' . $tablename . ' where ' . substr($filter, 5);

                $result = $this->query($query);
                $item[$tablename] = $result;


                $childResult[$key] = $item;

            }
        }

    }
}