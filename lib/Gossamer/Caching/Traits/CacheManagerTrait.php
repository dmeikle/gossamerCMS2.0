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
 * Date: 3/20/2017
 * Time: 9:06 PM
 */

namespace Gossamer\Caching\Traits;


use Gossamer\Caching\CacheManager;

trait CacheManagerTrait
{

    protected $cacheManager;

    public function setCacheManager(CacheManager $cacheManager) {
        $this->cacheManager = $cacheManager;
    }

    public function getCacheManager() {
        return $this->cacheManager;
    }
}