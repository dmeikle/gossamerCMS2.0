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
 * Time: 9:59 PM
 */

namespace Gossamer\Core\MVC;


use Gossamer\Core\Components\Preferences\Managers\UserPreferencesManager;
use Gossamer\Core\Components\Preferences\UserPreferences;
use Gossamer\Core\Navigation\Pagination;
use Gossamer\Essentials\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Horus\Filters\FilterEvents;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Core\System\KernelEvents;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Neith\Logging\LoggingInterface;

abstract class AbstractController
{

    protected $model;
    protected $view;
    protected $logger;
    protected $httpRequest;
    protected $httpResponse;

    use \Gossamer\Set\Utils\ContainerTrait;

    public function __construct(AbstractModel $model, AbstractView $view, LoggingInterface $logger, HttpRequest &$httpRequest, HttpResponse &$httpResponse) {
        $this->model = $model;
        $this->view = $view;
        $this->logger = $logger;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    protected function render($data = array()) {
        if (is_string($data)) {
            $data = array($data);
        }

        //run through any filters that may need to perform serialization on any results first
        $this->httpRequest->setAttribute('REQUEST_RESULT_DATA', $data);
        runFilters($this->httpRequest->getSiteParams()->getSitePath(). DIRECTORY_SEPARATOR . $this->httpRequest->getNodeConfig()['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'filters.yml', $this->httpRequest->getRequestParams()->getYmlKey(),FilterEvents::FILTER_SERIALIZATION);
       
        runFilters($this->httpRequest->getSiteParams()->getConfigPath() . 'filters.yml', 'all',FilterEvents::FILTER_SERIALIZATION);
         //get the result from the filters and continue on
        $data = $this->httpRequest->getAttribute('REQUEST_RESULT_DATA');
        //free that memory
        $this->httpRequest->setAttribute('REQUEST_RESULT_DATA',null);

        //notify the system that we have completed our request and are ready to render a response
        $event = new Event(KernelEvents::REQUEST_END, $data);

        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::REQUEST_END, $event);
        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), KernelEvents::REQUEST_END, $event);


        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::RESPONSE_START, $event);
        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), KernelEvents::RESPONSE_START, $event);

        $data = $event->getParams();

        return $this->view->render($data);
    }

    /**
     * @return array
     */
    public function index() {

        $result = $this->model->index(array('data' => ''));
        if(!array_key_exists('data', $result)) {
            $result['data'] = '';
        }

        return $this->render($result);
    }

    /**
     *
     */
    public function autocomplete() {
        $params = $this->httpRequest->getQueryParameters();

        return $this->render($this->model->autocomplete($params));
    }

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    public function listall($offset = 0, $limit = 50) {
        $params = $this->httpRequest->getRequestParams()->getQueryStringParameters();

       // ?orderby=category&direction=asc&directive::OFFSET=0&directive::LIMIT=10&directive::ORDER_BY=id&directive::DIRECTION=desc
        if(array_key_exists('orderby', $params)) {
            $params['directive::ORDER_BY'] = $params['orderby'];
            unset($params['orderby']);
        }
        if(array_key_exists('direction', $params)) {
            $params['directive::DIRECTION'] = $params['direction'];
            unset($params['direction']);
        }
        $result = $this->model->listall($offset, $limit, null, $params);

        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->getPagination($result[$this->model->getEntity() . 'sCount']['rowCount'], $offset,$limit);
            //CP-33 changed to json output for new Angular based page draws
            // $result['pagination'] = $pagination->getPaginationJson($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }
    
        return $this->render($result);
    }

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    public function listMinimal($offset = 0, $limit = 50) {
        $result = $this->model->listallWithParams(array('directive::OFFSET' => $offset, 'directive::LIMIT' => $limit), 'listminimal');


        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->getPagination($result[$this->model->getEntity() . 'sCount']['rowCount'], $offset,$limit);
            //CP-33 changed to json output for new Angular based page draws
            // $result['pagination'] = $pagination->getPaginationJson($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }

        return $this->render($result);
    }

    /**
     * listallReverse - retrieves rows based on offset, limit
     *
     * @param int offset    database page to start at
     * @param int limit     max rows to return
     */
    public function listallReverse($offset = 0, $limit = 20, $customVerb = null) {
        $params = $this->httpRequest->getRequestParams()->getQueryStringParameters();
        $params['directive::OFFSET'] = $offset;
        $params['directive::LIMIT'] = $limit;
        if(array_key_exists('orderby', $params)) {
            $params['directive::ORDER_BY'] = $params['orderby'];
            unset($params['orderby']);
        }
        if(array_key_exists('direction', $params)) {
            $params['directive::DIRECTION'] = $params['direction'];
            unset($params['direction']);
        }
        $result = $this->model->listallReverse($offset, $limit, $customVerb, $params);
     

        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->getPagination($result[$this->model->getEntity() . 'sCount']['rowCount'], $offset,$limit);
            //CP-33 changed to json output for new Angular based page draws
           // $result['pagination'] = $pagination->getPaginationJson($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }

        return $this->render($result);
    }

    /**
     * get - display an input form based on requested id
     *
     * @param  id    primary key of item to edit
     */
    public function get($id) {
        $locale = $this->getDefaultLocale();

        $params = array(
            'id' => ($id),
            'locale' => $locale['locale']
        );
        
        $result = $this->model->get($params);

        return $this->render($result);
    }

    /**
     * save - saves/updates row
     *
     * @param  id    primary key of item to save
     */
    public function save($id = null) {

        $post = $this->httpRequest->getRequestParams()->getPost();

        if('0' == $id) {

            $id = null;
        }
        if (!is_null($id) && !array_key_exists('id', $post)) {
            $post['id'] = $id;
        }
        $post = $this->addCustomSaveParams($post);
        $result = $this->model->save($post);

        $params = array('entity' => $this->model->getEntity(true), 'result' => $result, 'id' => $id);
        $event = new Event('save_success', $params);
        $this->container->get('EventDispatcher')->dispatch($this->httpRequest->getRequestParams()->getYmlKey(), 'save_success', $event);
        $nodeConfig = $this->httpRequest->getNodeConfig();

        if(array_key_exists('redirectUrl', $nodeConfig)) {
            header("Location: " . $nodeConfig['redirectUrl']);
            exit;
        }
        
        return $this->render($result);
    }

    private function addCustomSaveParams(array $post) {
        $nodeConfig = $this->httpRequest->getNodeConfig();
        if(array_key_exists('saveParams', $nodeConfig)) {
            foreach($nodeConfig['saveParams'] as $key => $value) {
                $post[$key] = $value;
            }
        }

        return $post;
    }

    /**
     * delete - removes a row from the database
     *
     * @param int id    primary key of item to delete
     */
    public function delete($id) {
        $params = array(
            'id' => $id
        );

        $result = $this->model->delete($params);

        return $this->render($result);
    }

    /**
     * method for building forms within the view to be called if needed by
     * child classes
     *
     * @param FormBuilderInterface $model
     * @param array $values
     * @throws Exception
     */
    protected function drawForm(FormBuilderInterface $model, array $values = null)
    {
        throw new \Exception('drawFrom not overwritten by child class');
    }

    /**
     * @param $id
     * @return array
     */
    public function setInactive($id)
    {
        $result = $this->model->setInactive($id);

        return $this->render($result);
    }

    /**
     * @param $key
     * @return array
     */
    public function setInactiveByKey($key) {
        return $this->render($this->model->setInactiveByKey($key));
    }


    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    public function search($offset, $limit) {
        $params = array_filter($this->httpRequest->getRequestParams()->getQueryStringParameters());


        //set precision if it exists in node config for routing.yml
        $config = $this->httpRequest->getNodeConfig();
        $params['precision'] = (array_key_exists('precision', $config)) ? $config['precision'] : 'false';

        $result = $this->model->search($offset, $limit, $params);

        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->getPagination($result[$this->model->getEntity() . 'sCount']['rowCount'], $offset,$limit);
            //CP-33 changed to json output for new Angular based page draws
            // $result['pagination'] = $pagination->getPaginationJson($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }
        
        return $this->render($result);
    }
    
    protected function getDefaultLocale() {
        //check to see if it's in the query string - a menu request perhaps?
        $params = $this->httpRequest->getRequestParams()->getQueryStringParameters();
       
        if (!is_null($params) && array_key_exists('locale', $params)) {
            return array('locale' => $params['locale']);
        }

        $manager = new UserPreferencesManager($this->httpRequest);
        $userPreferences = $manager->getPreferences();

        if (!is_null($userPreferences) && $userPreferences instanceof UserPreferences && strlen($userPreferences->getDefaultLocale()) > 0) {
            return array('locale' => $userPreferences->getDefaultLocale());
        }

        $config = $this->httpRequest->getAttribute('defaultPreferences');

        return $config['default_locale'];
    }



    public function passThroughGet() {

        $rawQueryParams = $this->httpRequest->getRequestParams()->getQueryStringParameters();

        $uri = $this->httpRequest->getRequestParams()->getUri();
        $uri = $this->stripComponentNameFromUri($uri);

        $result = $this->model->listallWithParams($rawQueryParams, $uri);

        return $this->render(is_array($result) ? $result : array($result));
    }

    private function stripComponentNameFromUri($uri) {

        $items = explode('/', $uri);
        array_shift($items);

        return implode('/', $items);
    }

    public function passThroughPost() {

        //find any querystring params if they exist (eg: apikey is passed on uri in older versions)
        $rawQueryParams = $this->httpRequest->getRequestParams()->getQueryStringParameters();
        $queryParams = '';
        foreach($rawQueryParams as $key => $value) {
            if($key != 'apikey') {
                $queryParams .= '&' . $key .'=' . $value;
            }
        }

        $segments = $this->httpRequest->getRequestParams()->getUrlSegments();

        //drop the module name
        array_shift($segments);
        $uri = $this->httpRequest->getRequestParams()->getUri();
        $uri = $this->stripComponentNameFromUri($uri);

        $uri .= ((strlen($queryParams) > 0)? '?'.substr($queryParams,1) : '');
        $uri = $this->removeQueryStringKey($uri, 'apikey');

        $params = $this->httpRequest->getRequestParams()->getPost();
      
        $result = $this->model->saveCustom($params, $uri);

        return $this->render(is_array($result) ? $result : array($result));
    }

    private function removeQueryStringKey($url, $key) {
        $pieces = explode('?', $url);
        $firstSegment = array_shift($pieces);
        $pieces = implode('', array_filter($pieces));
        parse_str($pieces, $filteredList);

        unset($filteredList['apikey']);

        $retval = implode('&', $filteredList);
        return $firstSegment . (strlen($retval) > 0 ? '?' . $retval : '');

    }

    public function redirect() {
        $nodeConfig = $this->httpRequest->getNodeConfig();

        if(!array_key_exists('url', $nodeConfig[$this->httpRequest->getYmlKey()])) {
            throw new KeyNotSetException('url not set for controller redirect on ' . $this->httpRequest->getYmlKey());
        }

        header("Location: " . $nodeConfig[$this->httpRequest->getYmlKey()]['url']);
        exit;
    }
}