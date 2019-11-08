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
 * Date: 9/2/2017
 * Time: 7:38 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Commands;


use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Traits\DocumentPreparationTrait;

class SaveCommand extends AbstractCommand
{

    use DocumentPreparationTrait;

    public function execute($params = array(), $requestParams = array())
    {

        file_put_contents(__DEBUG_OUTPUT_PATH, "saving\r\n", FILE_APPEND);
        file_put_contents(__DEBUG_OUTPUT_PATH, "params\r\n". print_r($requestParams, true), FILE_APPEND);
        
        $this->prepare($this->entity, $requestParams);
        $this->populateDocument($this->entity, $requestParams);
         
        file_put_contents(__DEBUG_OUTPUT_PATH,  "entity\r\n".print_r($this->entity->toArray(), true), FILE_APPEND);
        $id = $requestParams['id'];
        $this->getBucket($this->entity)->upsert($id, $this->entity->toArray());
        $result = $this->getBucket()->get($id);

        $object = array(json_decode(json_encode($result->value),true));

        //file_put_contents(__DEBUG_OUTPUT_PATH, "result\r\n". print_r($object, true), FILE_APPEND);
        $retval = array_shift($object);

        //a quick fix for now so we don't display passwords, until I can write a configuration
        //for this
        if(array_key_exists('password', $retval)) {
            unset($retval['password']);
        }
        
        return array($this->entity->getClassName() => $retval);
    }



    protected function setRandomId(array &$params) {
        if(array_key_exists('id', $params)) {
            return;
        }

        $params['id'] = uniqid();
    }



    protected function populateSubArray(Document &$document, array $params, Document $subDocument, $key = null)
    {
        //no need to do any work - just go back
        if(!array_key_exists($key, $params) && !($subDocument instanceof  DefaultValuesInterface)) {
            file_put_contents(__DEBUG_OUTPUT_PATH, "not an interface - returning\r\n", FILE_APPEND);
            return;
        }
        //define the key
        if(is_null($key)) {
            $key = $subDocument->getClassName().'s';
        }
        //we have defaults in the event nothing exists - let's use them and go back
        if(!array_key_exists($key, $params) && $subDocument instanceof  DefaultValuesInterface) {
            file_put_contents(__DEBUG_OUTPUT_PATH, "key found - populating\r\n", FILE_APPEND);
            $document->set($key, $subDocument->getDefaults());

            return;
        }

        file_put_contents(__DEBUG_OUTPUT_PATH, "still here\r\n", FILE_APPEND);
        $this->prepare($subDocument, $params);
        unset($params['id']);
        $this->populateDocument($subDocument, $params[$key]);

        $document->set($key, $subDocument->toArray());
    }

    /**
     * @param Document $document
     * @param array $params
     * @param Document $subDocument
     * @param null $key
     *
     * used for documents that have a nested array of ROWS to them
     *
     * eg:
     * Member:
     *  MemberRoles:
     *      {
     *          date: 2014-02-01
     *          role: regular_member
     *      }
     *      {
     *          date: 2014-02-20
     *          role: special_member
     *      }
     *
     * In this instance, MemberRoles is a list of rows - we need to handle them differently than
     * populating a standard document
     */
    protected function populateSubArrayNestedValues(Document &$document, array $params, Document $subDocument, $key = null)
    {
        //no need to do any work - just go back
        if(!array_key_exists($key, $params) && !($subDocument instanceof  DefaultValuesInterface)) {
            return;
        }
        //define the key
        if(is_null($key)) {
            $key = $subDocument->getClassName().'s';
        }

        //we have defaults in the event nothing exists - let's use them and go back
        if(!array_key_exists($key, $params) && $subDocument instanceof  DefaultValuesInterface) {
            $document->set($key, $subDocument->getDefaults());

            return;
        }

        $listOfDocuments = array();
        foreach($params[$key] as $row) {
            $this->populateDocument($subDocument, $row);
            $listOfDocuments[] = $subDocument->toArray();
        }

        $document->set($key, $listOfDocuments);
    }
}