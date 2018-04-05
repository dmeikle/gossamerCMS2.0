<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/** *
 * Author: dave
 * Date: 9/26/2016
 * Time: 3:57 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Documents;


class Document
{

    protected $documentChanged = false;

    protected $tablename;

    /**
     * can be used as an internal configuration, but can be overwritten in each class if needed
     *
     * @var array
     */
    protected $fields = array('id');

    protected $values = array();


    public function __construct() {
        $this->tablename = $this->stripNamespacing(get_class($this)) . 's';
    }

    public function getNamespace() {
        $reflector = new \ReflectionClass($this); // class Foo of namespace A

        return $reflector->getNamespaceName();
    }

    private function stripNamespacing($namespacedEntity) {
        $chunks = explode('\\', $namespacedEntity);

        return array_pop($chunks);
    }

    public function getIdentityField() {
        return $this->tablename;
    }

    public function getDocumentKey() {
        return strtolower($this->tablename) . '::' . $this->getId();
    }

    public function getId() {
        if (!array_key_exists('id', $this->values)) {
            return '';
        }

        return $this->values['id'];
    }

    public function getClassName() {
        $reflect = new \ReflectionClass($this);

        return $reflect->getShortName();
    }

    /**
     * assigns the values of the passed in params to the document.
     *
     * @param array $params - the values to assign
     * @param array|null $fields - the field names to look for in the array
     */
    public function populate(array $params, array $fields = null) {

        if (!is_null($fields)) {
            $this->fields = $fields;
        }

        foreach ($params as $key => $value) {


            if (!in_array($key, $this->fields)) {
                continue;
            }
            $value = $this->decodeJson($value);

            $this->set($key, $value);
        }

        if (array_key_exists('lastModified', $fields)) {
            if (!array_key_exists('lastModified', $params)) {
                $this->set('lastModified', date('Y-m-d h:i:s a', time()));
            }
        }
    }

    private function decodeJson($value) {
        if (is_array($value)) {
            return $value;
        }
        if (substr($value, 0, 1) == '[') {
            return json_decode($value, true);
        }
        return $value;
    }

    public function populateArray(array $params, array $fields = null) {
        if (!is_null($fields)) {
            $this->fields = $fields;
        }

        $retval = array();
        foreach ($params as $key => $row) {
            $newRow = array_intersect_key($row, array_flip($this->fields));

            if (array_key_exists('lastModified', $this->fields)) {
                if (!array_key_exists('lastModified', $newRow)) {
                    $newRow['lastModified'] = date('Y-m-d h:i:s a', time());
                }
            }
            $retval[] = $newRow;
        }

        $this->values = $retval;
    }


    public function populateNested(array $params, array $schema) {
        //first do the main fields

        foreach ($schema['fields'] as $key) {
            if (array_key_exists($key, $params)) {
                $this->set($key, $params[$key]);
            }
        }
        $joins = $schema['joins'];
        //now do the join fields
        foreach ($joins[0] as $documentName => $fields) {

            if (array_key_exists($documentName, $params)) {
                $subarray = array();

                foreach ($params[$documentName] as $key => $value) {

                    if (in_array($key, $fields)) {
                        $subarray[$key] = $value;
                    }
                }
                $this->set($documentName, $subarray);
            }
        }
    }


    public function get($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }

    public function set($key, $value) {
        if (!$this->documentChanged && (!array_key_exists($key, $this->fields) || !isset($this->fields[$key]) || $this->fields[$key] != $value)) {
            $this->documentChanged = true;
        }

        $this->values[$key] = $value;
    }

    public function setArray($key, $value) {
        if (count($value) == 0) {
            return;
        }

        if (!$this->documentChanged && (!array_key_exists($key, $this->fields) || !isset($this->fields[$key]) || $this->fields[$key] != $value)) {
            $this->documentChanged = true;
        }

        $this->values[$key] = $value;
    }

    public function getAll() {
        return $this->values;
    }

    public function toJson() {
        return json_encode($this->getAll());
    }

    public function toArray() {
        return $this->getAll();
    }
}