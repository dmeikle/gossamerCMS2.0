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
 * Date: 3/5/2017
 * Time: 10:00 PM
 */

namespace Gossamer\Core\MVC;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;

abstract class AbstractModel
{

    const METHOD_DELETE = 'delete';
    const METHOD_SAVE = 'save';
    const METHOD_PUT = 'put';
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const VERB_LIST = 'list';
    const VERB_DELETE = 'delete';
    const VERB_GET = 'get';
    const VERB_SEARCH = 'search';
    const VERB_SAVE = 'save';
    const DIRECTIVES = 'directives';

    protected $entity;

    protected $childNamespace;

    protected $tablename;

    protected $httpResponse;

    protected $httpRequest;

    protected $logger;

    use \Gossamer\Set\Utils\ContainerTrait;

    public function __construct(HttpRequest $httpRequest = null, HttpResponse $httpResponse = null, LoggingInterface $logger = null) {

        $this->childNamespace = str_replace('\\', DIRECTORY_SEPARATOR, __NAMESPACE__);
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->entity = $this->getClassName();
        $this->tablename = strtolower($this->entity) . 's';
    }

    /**
     * @param null $datasourceKey   - the name of the ymlkey for the datasource configuration
     * @return mixed
     */
    protected function getDatasource($datasourceKey = null) {

        if(is_null($datasourceKey)) {
            $nodeConfig = $this->httpRequest->getNodeConfig();
            return $this->container->get('EntityManager')->getConnection($nodeConfig[$this->httpRequest->getRequestParams()->getYmlKey()]['defaults']['datasource']);
        }

        return $this->container->get('EntityManager')->getConnection($datasourceKey);
    }

    protected function getClassName() {
        $namespacedClass = get_called_class();

        $chunks = explode('\\',$namespacedClass);
        $modelName = array_pop($chunks);
        unset($chunks);

        return substr($modelName, 0, strlen($modelName) - 5);
    }


    /**
     * accessor
     *
     * @return string
     */
    function getTablename() {
        return $this->tablename;
    }

    /**
     *
     * @param type $stripNamespace
     *
     * @return string
     */
    public function getEntity($stripNamespace = false) {
        if ($stripNamespace) {
            $pieces = explode('\\', $this->entity);

            return array_pop($pieces);
        }
        return $this->entity;
    }



    /**
     * @param array $params
     * @return array
     */
    public function index(array $params) {
        return $params;
    }

    /**
     * queries the datasource and deletes the record
     *
     * @param type $offset
     * @param type $rows
     *
     * @return array
     */
    public function delete(array $params) {

        return $this->getDatasource()->query(self::METHOD_DELETE, $this, self::VERB_DELETE, $params);
    }

    /**
     * queries the datasource and returns the result
     *
     * @param type $offset
     * @param type $rows
     *
     * @return array
     */
    public function listallArray(array $params) {

        return $this->getDatasource()->query(self::METHOD_GET, $this, self::VERB_LIST, $params);
    }

    /**
     * performs a save to the datasource
     *
     * @param int $id
     *
     * @return type
     */
    public function save(array $params) {

        $data = $this->getDatasource()->query(self::METHOD_POST, $this, self::VERB_SAVE, $params);

        return $data;
    }

    /**
     * performs a save to the datasource
     *
     * @param int $id
     *
     * @return type
     */
    public function saveCustom(array $params, $customVerb = null) {

        $data = $this->getDatasource()->query(self::METHOD_POST, $this, (is_null($customVerb)? self::VERB_SAVE : $customVerb), $params);

        return $data;
    }

    /**
     *
     * @param type $offset
     * @param type $rows
     * @param type $customVerb
     * @return type
     */
    public function listall($offset = 0, $rows = 20, $customVerb = null, array $params = null) {

        if (is_null($params)) {
            $params = array();
        }
        $params['directive::OFFSET'] = $offset;
        $params['directive::LIMIT'] = $rows;
        if(array_key_exists('orderby', $params)) {
            $params['directive::ORDER_BY'] = $params['orderby'];
            unset($params['orderby']);
        }
        if(array_key_exists('direction', $params)) {
            $params['directive::DIRECTION'] = $params['direction'];
            unset($params['direction']);
        }
        return $this->listallWithParams($params, $customVerb);
    }

    /**
     * queries the datasource in reverse order
     *
     * @param int $offset
     * @param int $rows
     * @param string $customVerb
     *
     * @return array
     */
    public function listallReverse($offset = 0, $rows = 20, $customVerb = null, array $params = array()) {
        if(!array_key_exists('directive::ORDER_BY', $params)) {
            $params['directive::ORDER_BY'] = 'id';
        }
        if(!array_key_exists('directive::DIRECTION', $params)) {
            $params['directive::DIRECTION'] = 'desc';
        }

        return $this->listallWithParams($params, $customVerb);
    }

    /**
     * queries the database with custom passed in params and returns the result
     *
     * @param int $offset
     * @param int $rows
     * @param array $params
     * @param string $customVerb
     *
     * @return array
     */
    public function listallWithParams(array $params, $customVerb = null) {

        $data = $this->getDatasource()->query(self::METHOD_GET, $this, (is_null($customVerb) ? self::VERB_LIST : $customVerb), $params);

        return $data;
    }



    /**
     * @param array $params
     * @return array
     */
    public function autocomplete(array $params) {

        return $this->listallWithParams(0, 20, $params, 'autocomplete');
    }

    /**
     * sets a row inactive (soft delete) in the database - intended for SQL databases
     *
     * @param  int $id
     *
     */
    public function setInactive($id) {

        $params = array(
            'id' => intval($id),
            'isActive' => '0'
        );

        $data = $this->getDatasource()->query(self::METHOD_POST, $this, self::VERB_SAVE, $params);

        return $data;
    }

    /**
     * sets a row inactive (soft delete) in the database - intended for NoSQL databases
     *
     * @param $key
     * @return mixed
     */
    public function setInactiveByKey($key) {

        $params = array(
            'id' => $key,
            'isActive' => '0'
        );

        $data = $this->getDatasource()->query(self::METHOD_POST, $this, 'setinactive', $params);

        return $data;
    }

    /**
     * retrieves a single row from the datasource
     *
     * @param int $id
     *
     * @return array
     */
    public function get(array $params, $customVerb = null) {

        $data = $this->getDatasource()->query(self::METHOD_GET, $this, is_null($customVerb)? self::VERB_GET : $customVerb, $params);

        if (is_array($data) && array_key_exists($this->entity, $data)) {
            $retval = array();
            if(array_key_exists('0', $data[$this->entity])) {
                $retval[$this->entity] = current($data[$this->entity]);
            } else {
                $retval[$this->entity] = $data[$this->entity];
            }
                         
            return $retval;
        }

        return $data;
    }

    /**
     * @param $offset
     * @param $limit
     * @param array $params
     * @return array
     */
    public function search($offset, $limit, array $params) {

        $params = array_merge($params, array(
            'directive::OFFSET' => $offset, 'directive::LIMIT' => $limit
        ));

        $data = $this->getDatasource()->query(self::METHOD_GET, $this, 'search', $params);

        return $data;
    }

    protected function getDefaultLocale() {
        $locale = array(
            'locale' => 'en_US'
        );

        return $locale;
    }

    public function listMinimal($params = array()) {

        $result = $this->getDatasource()->query(self::METHOD_GET, $this, 'listminimal', $params);

        return $result;
    }
}