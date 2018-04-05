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
 * Time: 9:51 PM
 */

namespace Gossamer\Core\Configuration;


use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class YamlLoader extends Parser implements ConfigLoader
{
    public function setFilePath($ymlFilePath) {
        $this->ymlFilePath = $ymlFilePath;
    }

    public function loadConfig() {
        return @Yaml::parse(file_get_contents($this->ymlFilePath));
    }
}