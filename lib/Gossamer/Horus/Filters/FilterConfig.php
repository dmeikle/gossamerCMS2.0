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
 * Date: 3/2/2017
 * Time: 11:08 PM
 */

namespace Gossamer\Horus\Filters;


class FilterConfig
{
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function get($key) {
        
        if(array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return null;
    }
}