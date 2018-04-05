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
 * Date: 3/18/2017
 * Time: 8:02 PM
 *
 *
 * This file is used by the database and API versions of routing.  Since urls can be customized from the HTML server
 * and not relying so much upon a single 'segment' to determine which component to point to (because the REST
 * calls are usually indicative of the tables, not the modules) it is more reliable to make a routing file based
 * upon possible URL patterns.
 */
class LoadAllDirectoryConfigurations
{
    public function execute() {
        //first list the core components
        $coreDirectories = $this->listDirectories(__SITE_PATH . 'lib/Gossamer' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Components' . DIRECTORY_SEPARATOR);
        $coreConfigurations = $this->parseDirectoryConfigurations($coreDirectories, 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR, 'bootstrap.yml');

        $componentDirectories = $this->listDirectories(__SITE_PATH . 'src' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR);
        $componentConfigurations = $this->parseDirectoryConfigurations($componentDirectories,'src' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR, 'bootstrap.yml');

        $extensionDirectories = $this->listDirectories(__SITE_PATH . 'src' . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR);
        $extensionConfigurations = $this->parseDirectoryConfigurations($extensionDirectories, 'src' . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR, 'bootstrap.yml');

        $list = array_merge($coreConfigurations, $componentConfigurations,$extensionConfigurations);

        return $this->generateInitialRoutingFile($list);
    }

    protected function generateInitialRoutingFile(array $list) {
        $retval = '';

        foreach ($list as $route => $uri) {
            foreach($uri as $row) {
                $retval .= "'$row':\r\n    component: '$route" . 'config' . DIRECTORY_SEPARATOR . "bootstrap.yml'\r\n";
            }
            $retval .= "\r\n";
        }

        return $retval;
    }

    protected function parseDirectoryConfigurations(array $directories, $directoryPath, $filename) {
        $config = array();

        foreach ($directories as $directory) {
            $directoryConfig = $this->loadConfig(__SITE_PATH  . $directoryPath . $directory . 'config' . DIRECTORY_SEPARATOR . $filename);

            if(!is_null($directoryConfig)) {
                $config[$directoryPath . $directory] = array_column($directoryConfig, 'pattern');
            }
        }

        return $config;
    }

    protected function loadConfig($configPath) {
        $loader = new \Gossamer\Essentials\Configuration\YamlLoader();
        $loader->setFilePath($configPath);

        return $loader->loadConfig();
    }

    protected function listDirectories($rootPath) {
        echo "listdirectories: $rootPath\r\n";
        $retval = array();
        $directories = scandir($rootPath);
        foreach ($directories as $key => $directory) {
            if (!in_array($directory, array(".", ".."))) {
                if (is_dir($rootPath . DIRECTORY_SEPARATOR . $directory)) {
                    array_push($retval, $directory . DIRECTORY_SEPARATOR);
                }
            }
        }

        return $retval;
    }

}