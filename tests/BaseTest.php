<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests;
use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Horus\Http\RequestParams;
use Gossamer\Pesedget\Entities\EntityManager;
use Gossamer\Set\Utils\Container;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/16/2018
 * Time: 5:35 PM
 */
class BaseTest extends TestCase
{
    const GET = 'GET';
    const POST = 'POST';

    private $logger = null;

    //these can get instantiated by the child class
    protected static $requestParams;
    protected $container = null;

    protected $httpRequest;

    protected $httpResponse;

    use LoadConfigurationTrait;

    public static function setUpBeforeClass() {
        self::$requestParams = new RequestParams();

    }
    
    
    protected function getHttpRequest($uri, RequestParams $requestParams, $requestMethod) {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';

        if($requestMethod == 'POST') {
            parse_str(implode('&',$requestParams->getPost()), $_POST);
            $_SERVER['QUERY_STRING'] ='';
        }else{
            $_SERVER['QUERY_STRING'] = $requestParams;
        }
        $this->setURI($uri);
        global $siteParams;
        $request = new \Gossamer\Horus\Http\HttpRequest($requestParams, $siteParams);

        return $request;
    }

    protected function getHttpResponse() {
        return new HttpResponse();
    }

    protected function getLogger() {
        if(is_null($this->logger)) {
            $this->logger = buildLogger();
        }

        return $this->logger;
    }

    public function setRequestMethod($method) {
        define("__REQUEST_METHOD", $method);
    }

    public function setURI($uri) {
        if(!defined('__URI')) {
            define('__URI', $uri);
        }
        if(!defined('__REQUEST_URI')) {
            define("__REQUEST_URI", $uri . '/');
        }

    }

//    public function getDBConnection() {
//
//        $conn = new \Gossamer\Pesedget_bak\Database\DBConnection($this->getCredentials());
//
//        return $conn;
//    }


    protected function getRestConnection($datasourceKey){

        $rest = new RestDataSource($this->getLogger());
        $rest->setDatasourceKey($datasourceKey);
        return $rest;
    }

    protected function getCredentials() {
        $credentials = array();
        $credentials['baseUrl'] = 'http://127.0.0.1:8060';
        $credentials['format'] = 'json';
        $credentials['headers']['serverName'] = 'vancouver';
        $credentials['headers']['serverAuth'] = '$1$lIDKkGiyJVn2bZSQdxwEYW0';

        return $credentials;
    }

    protected function getDBCredentials() {
        return $this->loadConfig(__CONFIG_PATH . 'phpunit.credentials.yml');
    }

    
    protected function getContainer($httpRequest, $httpResponse) {
pr($this->container);
        if(is_null($this->container )) {
            echo "container is null\r\n";
            $this->container = new Container();
            $entityManager = new EntityManager($this->getDBCredentials());
            //instantiate the database entity manager
            $this->container->set('EntityManager',  $entityManager);

            $eventDispatcher = new EventDispatcher($this->getLogger(), $httpRequest, $httpResponse, 'GET', 'phpunit_test');
            $this->container->set('EventDispatcher',  $eventDispatcher);
        }
pr($this->container);
        return $this->container;
    }

    protected function getView($ymlKey, $request, $response) {
        $array = array();
        $logger = $this->getLogger();

        $view = new PHPUnitView($logger,$ymlKey,$array,$request, $response);
        $view->setContainer($this->getContainer($request,$response));

        return $view;
    }
    
    protected function getRequestParams(array $get, array $post, array $uriParameters, string $method) {
        $requestParams = new \Gossamer\Horus\Http\RequestParams();

        $requestParams->setHeaders(array());
        $requestParams->setPost($post);
        $requestParams->setQuerystring($get);
        $requestParams->setUriParameters($uriParameters);
        $requestParams->setServer(array());
        $requestParams->setLayoutType($this->getLayoutType());
        $requestParams->setMethod($method);
        $requestParams->setSiteURL('phpunit_site_test');

        // $requestParams->setUri()
        return $requestParams;
    }

    private function getLayoutType() {
       

        return array('isMobile' => 0, 'isTablet' => 0, 'isDesktop' => 1);
    }
//    protected static $dbh;
//
//    public static function setUpBeforeClass()
//    {
//        self::$dbh = new PDO('sqlite::memory:');
//    }
//
//    public static function tearDownAfterClass()
//    {
//        self::$dbh = null;
//    }
}