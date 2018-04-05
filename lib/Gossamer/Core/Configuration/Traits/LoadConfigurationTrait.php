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
 * Time: 11:41 PM
 */

namespace Gossamer\Core\Configuration\Traits;


use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;

trait LoadConfigurationTrait
{


    protected function loadConfig($configPath) {
        $loader = new \Gossamer\Core\Configuration\YamlLoader();
        $loader->setFilePath($configPath);

        $config = $loader->loadConfig();

        return is_null($config) ? array() : $config;
    }

    protected function iterateComponentConfigurations() {

        $retval = array();
        $retval[] = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'bootstrap.yml');


//        if (array_key_exists('langFiles', $bootstraps['all']['defaults'])) {
//            $langFilesList = $bootstraps['all']['defaults']['langFiles'];
//        }

        $extensions = $this->getDirectoryList($this->httpRequest->getSiteParams()->getSitePath() . 'src' . DIRECTORY_SEPARATOR . 'extensions');
        $config = $this->findCurrentNodeConfig($this->httpRequest->getRequestParams()->getYmlKey(), $extensions);
        if ($config == false) {
            $components = $this->getDirectoryList($this->httpRequest->getSiteParams()->getSitePath() . 'src' . DIRECTORY_SEPARATOR . 'components');
            $config = $this->findCurrentNodeConfig($this->httpRequest->getRequestParams()->getYmlKey(), $components);
            if ($config == false) {
                $coreComponents = $this->getDirectoryList($this->httpRequest->getSiteParams()->getSitePath() . 'src' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'components');
                $config = $this->findCurrentNodeConfig($this->httpRequest->getRequestParams()->getYmlKey(), $coreComponents);
                if (config === false) {
                    throw new KeyNotFoundException('Unable to locate YML key in bootstraps');
                }
            }
        }
        $retval[] = $config;

        return $retval;
    }

    protected function findCurrentNodeConfig($ymlkey, array $folderPath) {
        foreach ($folderPath as $folder) {
            $config = $this->loadConfig($folder . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.yml');
            if (is_array($config) && array_key_exists($ymlkey, $config)) {
                return $config[$ymlkey];
            }
        }

        return false;
    }

    protected function getDirectoryList($path) {

        $retval = array();

        if ($handle = opendir($path)) {
            $blacklist = array('.', '..', 'somedir', 'somefile.php');
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $blacklist)) {
                    $retval[] = $path . DIRECTORY_SEPARATOR . $file;
                }
            }
            closedir($handle);
        }

        return $retval;
    }
}