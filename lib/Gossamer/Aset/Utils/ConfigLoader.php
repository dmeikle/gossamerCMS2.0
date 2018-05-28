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
 * Date: 5/27/2018
 * Time: 12:00 AM
 */

namespace Gossamer\Aset\Utils;


use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Essentials\Configuration\SiteParams;
use Validation\ConfigLoaderInterface;

class ConfigLoader implements ConfigLoaderInterface
{
    private $config;
    
    use LoadConfigurationTrait;
    
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function loadConfig($filepath) {

    }

    public function getConfig() {
        return $this->config;
    }

    public function getNode($key) {
        echo "getting $key\r\n";
        if(!array_key_exists($key, $this->config)) {
            pr($this->config);
            echo $key;
        }
        return $this->config[$key];
    }
}