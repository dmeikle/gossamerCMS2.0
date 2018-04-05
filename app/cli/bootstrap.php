<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Vancouver');

$sitePath = realpath(dirname(__FILE__));
$sitePath = str_replace('/app/cli', '', $sitePath) . DIRECTORY_SEPARATOR;

define('__SITE_PATH', $sitePath);
define('__CACHE_DIRECTORY', $sitePath . '/app/cache');
define('__DEBUG_OUTPUT_PATH', '/var/www/binghan.com/logs/phpunit.log');
define('__CONFIG_PATH', $sitePath . '/app/config/');

$_SERVER = array('REQUEST_METHOD'=>'GET',
    'SERVER_PORT' => 80,
    'HTTP_HOST' => 'CLI');

//include_once('phpunit.configuration.php');
require_once(__SITE_PATH . '/vendor/composer/ClassLoader.php');
require_once(__SITE_PATH . '/vendor/autoload.php');

$loader = new Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('components', __SITE_PATH . '/src');
$loader->add('extensions', __SITE_PATH . '/src');
$loader->add('core', __SITE_PATH . '/app');
$loader->add('exceptions', __SITE_PATH . '/app/');
$loader->add('Gossamer', __SITE_PATH . '/lib');
$loader->add('RestClient', __SITE_PATH . '/vendor/tcdent/php-restclient');
$loader->add('Detection', __SITE_PATH . '/vendor/mobiledetect/mobiledetectlib');

$loader->add('Monolog', __SITE_PATH . '/vendor/monolog/monolog/src');

// activate the autoloader
$loader->register();

// to enable searching the include path (eg. for PEAR packages)
$loader->setUseIncludePath(true);

function super_unset($item) {
    try {
        if (is_object($item) && method_exists($item, "__destruct")) {
            $item->__destruct();
        }
    } catch (\Exception $e) {

    }
    //unset($item);
    $item = null;
}

$_SESSION = array();

function getSession($key = null) {
    echo "getting sesson";
    $session = $_SESSION;
    echo "got it";
    if(is_null($key)) {
        echo "return it";
        echo gettype($session);
        return $session;
    }
    return fixObject($session[$key]);
}

function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

function fixObject(&$object) {
    if (!is_object($object) && gettype($object) == 'object') {

        return ($object = unserialize(serialize($object)));
    }

    return $object;
}



$siteParams = new \Gossamer\Essentials\Configuration\SiteParams();
$siteParams->setSitePath($sitePath);
$siteParams->setConfigPath($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
$siteParams->setLogPath($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);
$siteParams->setSiteName( 'gcms');
$siteParams->setCacheDirectory($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
$siteParams->setDebugOutputPath('/var/www/binghan/logs/rest4_debug.log');
$siteParams->setIsCaching(true);


//file_put_contents(__DEBUG_OUTPUT_PATH, print_r($container->get('EntityManager')));

function pr($item) {
    echo '<pre>\r\n';
    print_r($item);
    echo'</pre>\r\n';
}

function getallheaders() {
    return array();
}

$bootStrapLoader = new \Gossamer\Core\Kernel\BootstrapLoader();

//request is for all parameters needed to complete the request
$httpRequest = new \Gossamer\Horus\Http\HttpRequest($bootStrapLoader->getRequestParams(), $siteParams);









