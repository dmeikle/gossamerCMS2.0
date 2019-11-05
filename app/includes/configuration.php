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
 * Date: 3/1/2017
 * Time: 8:30 PM
 */

// this is the very first file to be included by the index.php
$sitePath = str_replace('/web', '', $_SERVER['DOCUMENT_ROOT']); // realpath(dirname(__FILE__));

$url = str_replace('//', '/', $_SERVER['REQUEST_URI']);
$uriPieces = parse_url($url);
$requestURI = $uriPieces['path'];
if (substr($requestURI, -1, 1) != '/') {
    $requestURI .='/';
}

date_default_timezone_set('America/Vancouver');


$sitePath = realpath(dirname(__FILE__));

$sitePath = str_replace('/app/includes', '', $sitePath) . DIRECTORY_SEPARATOR;

define('__SITE_PATH', $sitePath);
define('__CACHE_DIRECTORY', $sitePath . '/app/cache');
define('__DEBUG_OUTPUT_PATH', '/var/www/guitartommys.com/2020/logs/debug.log');
define('__CONFIG_PATH', $sitePath . '/app/config/');

//now onto includes/init.php
