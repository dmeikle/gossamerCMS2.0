<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/18/2017
 * Time: 7:59 PM
 */

require_once 'bootstrap.php';

require_once 'classes/LoadAllDirectoryConfigurations.php';

$cmd = new LoadAllDirectoryConfigurations();

$routing = $cmd->execute();

$myfile = fopen(__SITE_PATH . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db-routing.yml', "w") or die("Unable to open file!");

fwrite($myfile, $routing);
fclose($myfile);