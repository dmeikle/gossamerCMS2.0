<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


//phpinfo();
//die('done');
session_start();

ini_set('default_charset', 'UTF-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type,Accept, JWT, Authorization, enctype, Pragma, Cache-Control');//enctype, Pragma, Cache-Control = file upload
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS, HEAD");
header('Access-Control-Allow-Credentials: true');

if(array_key_exists('HTTP_ORIGIN', $_SERVER) &&
	($_SERVER['HTTP_ORIGIN'] == 'http://localhost:4200' || 
	$_SERVER['HTTP_ORIGIN'] == 'http://localhost:8100' ||
 	$_SERVER['HTTP_ORIGIN'] == 'http://localhost:8002' ||
	$_SERVER['HTTP_ORIGIN'] == 'http://localhost:8080' ||
	$_SERVER['HTTP_ORIGIN'] == 'http://localhost:8000' ||
	$_SERVER['HTTP_ORIGIN'] == 'http://70.68.149.100')) { //my computer
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}
if ($_SERVER['REQUEST_METHOD']=='OPTIONS') {
    header("HTTP/1.1 200 OK");
    die(); 
}

//these are included in order of operation - do not change!
include_once('../app/includes/configuration.php');
include_once('../vendor/autoload.php');
include_once('../app/includes/init.php');
include_once('../app/includes/bootstrap.php');
 
error_log($httpRequest->getRequestParams()->getYmlKey());
//pr( $httpRequest->getNodeConfig());
use Gossamer\Core\System\Kernel;

$kernel = new Kernel($container, $container->get('Logger'), $httpRequest, $httpResponse);

$kernel->run();

super_unset($kernel);
super_unset($container);

if (session_id() !== '') {
    session_destroy();
}
