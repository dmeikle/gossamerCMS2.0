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
 * Date: 6/5/2017
 * Time: 10:20 PM
 */

namespace Gossamer\Core\Components\Caching\Managers;


class CacheManager extends \Gossamer\Caching\CacheManager
{

    protected function getCacheDirectory() {
        if (defined('__CACHE_DIRECTORY')) {
            return __CACHE_DIRECTORY;
        }

        if(!is_null($this->request)) {
            return $this->request->getSiteParams()->getCacheDirectory();
        }

        throw new \Exception('Cache Directory not configured');
    }
}