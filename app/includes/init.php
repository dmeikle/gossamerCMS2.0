<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/1/2017
 * Time: 8:31 PM
 */


$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('components', $sitePath . '/src');
$loader->add('Gossamer', $sitePath . '/lib');

$loader->add('extensions', $sitePath . '/src');
$loader->add('usercommands', $sitePath . '/src');
$loader->add('userentities', $sitePath . '/src');
$loader->add('controllers', $sitePath . '/app');
$loader->add('core', $sitePath . '/app');
$loader->add('database', $sitePath . '/app');
$loader->add('entities', $sitePath . '/app');
$loader->add('Exceptions', $sitePath . '/app');
$loader->add('filters', $sitePath . '/app');
$loader->add('libraries', $sitePath . '/app');
$loader->add('security', $sitePath . '/app');
$loader->add('plugins', $sitePath . '/plugins');

// activate the autoloader
$loader->register();

// to enable searching the include path (eg. for PEAR packages)
$loader->setUseIncludePath(true);


$siteParams = new \Gossamer\Essentials\Configuration\SiteParams();
$siteParams->setSitePath($sitePath);
$siteParams->setConfigPath($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
$siteParams->setLogPath($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);
$siteParams->setSiteName($_SERVER['SERVER_NAME']);
$siteParams->setCacheDirectory($sitePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
$siteParams->setDebugOutputPath('/var/www/binghan/logs/rest4_debug.log');
$siteParams->setIsCaching(true);
$siteParams->setSiteConfig(loadConfig($siteParams->getConfigPath() . 'config.yml'));



//file_put_contents(__DEBUG_OUTPUT_PATH, print_r($container->get('EntityManager')));

function pr($item) {
    echo '<pre>\r\n';
    print_r($item);
    echo '</pre>\r\n';
}

function buildLogger(\Gossamer\Essentials\Configuration\SiteParams $params) {

    $config = loadConfig($params->getConfigPath() . 'config.yml');

    $loggerConfig = $config['logger'];
    $loggerClass = $loggerConfig['class'];
    $logger = new $loggerClass('rest-service');
    $handlerClass = $loggerConfig['handler']['class'];
    $logLevel = $loggerConfig['handler']['loglevel'];
    $logFile = $loggerConfig['handler']['logfile'];

    $maxFiles = null;
    if (array_key_exists('maxfiles', $loggerConfig['handler'])) {
        $maxFiles = $loggerConfig['handler']['maxfiles'];
    }
    if (is_null($maxFiles)) {
        $logger->pushHandler(new $handlerClass($params->getLogPath() . $logFile, $logLevel));
    } else {
        $logger->pushHandler(new $handlerClass($params->getLogPath() . $logFile, $maxFiles, $logLevel));
    }

    return $logger;
}


function loadConfig($configPath, $ymlKey = null, $type = null, $keys = null) {
    $loader = new \Gossamer\Essentials\Configuration\YamlLoader();

    $loader->setFilePath($configPath);
    $config = $loader->loadConfig();

    if (!is_null($keys)) {
        if (is_null($config)) {
            throw new \Gossamer\Essentials\Configuration\Exceptions\FileNotFoundException($configPath . ' does not exist');
        }
       
        if (array_key_exists($ymlKey, $config)) {
            $config = $config[$ymlKey][$type];
            //check to see if it's just an empty file
            if(!is_array($config) || count($config) == 0) {
                return array();
            }
            foreach ($config as $index => $row) {
                if ($row['event'] != $keys) {
                    unset($config[$index]);
                }
            }

        } else {
            //nothing found for this yml key
            return array();
        }
    }elseif(!is_null($ymlKey)) {
        if(array_key_exists($ymlKey, $config)) {
            return $config[$ymlKey];
        }
    }

    return $config;
}

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

function renderResult(array $result) {

    if(array_key_exists('headers', $result)) {
        foreach ($result['headers'] as $header) {
            header($header);
        }
    }
    if(!array_key_exists('data', $result)) {
        die('no response generated');
    }
    print_r($result['data']);
    exit;
}


function getSession($key = null) {
    $session = $_SESSION;
    if(!is_array($session)) {
        $session = array();
    }
    if (is_null($key)) {
        return $session;
    }
    if(!array_key_exists($key, $session)) {
        return null;
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

//now onto includes/bootstrap.php

function runFilters($filterConfigPath, $ymlKey, $eventName) {

    global $httpRequest;
    global $httpResponse;
    global $eventDispatcher;
    global $filterService;
    try {

        $config = loadConfig($filterConfigPath, $ymlKey, 'filters', $eventName);
        $filterService->setFilters($config);

        $result = $filterService->filterRequest($httpRequest, $httpResponse, $eventName);
   
        if (is_array($result)) {
            renderResult($result);
        }

    }catch(\Gossamer\Essentials\Configuration\Exceptions\FileNotFoundException $e) {
        //nothing needed to run
    } catch (\Exception $e) {
        echo $e->getMessage();
        die;
        $params = array('code' => $e->getCode(), 'message' => $e->getMessage());
        $event = new \Gossamer\Horus\EventListeners\Event(\Gossamer\Core\System\KernelEvents::ERROR_OCCURRED, $params);
        $eventDispatcher->dispatch('all', \Gossamer\Core\System\KernelEvents::ERROR_OCCURRED, $event);

//    $view = new \core\views\HtmlErrorView($siteParams->getSitePath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'themes' .
//    DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $e->getCode() . '.htm');
//    $view->render();
//    die;
        
    }

}