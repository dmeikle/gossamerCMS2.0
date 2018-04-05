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
 * Time: 10:00 PM
 */

namespace Gossamer\Core\Components\Caching\Traits;


use Gossamer\Caching\CacheManager;

trait CachingTrait
{


     protected $MAX_FILE_LIFESPAN = 12000; //20 minutes

    //this value MUST be assigned by child class

    protected $key = null;

    protected $siteParams;

    protected function setSiteParams(array $config) {
        $this->siteParams = $config;
    }


    /**
     * save the values into cache
     *
     * @param type $key
     * @param type $values
     * @param type $static
     *
     * @return boolean
     */
    protected function deleteCache($key) {
        $manager = new CacheManager($this->logger);
        $manager->setHttpRequest($this->httpRequest);

        return $manager->deleteCache($key);
    }

    /**
     * save the values into cache
     *
     * @param type $key
     * @param type $values
     * @param type $static
     *
     * @return boolean
     */
    protected function saveValuesToCache($key, $values, $static = false) {
        $manager = new CacheManager($this->logger);
        $manager->setHttpRequest($this->httpRequest);

        return $manager->saveToCache($key, $values, $static);
    }

    /**
     * retrieve values stored in cache
     *
     * @param type $key
     * @param type $static
     * @return array|string
     */
    protected function getValuesFromCache($key, $static) {

        $manager = new CacheManager($this->logger);
        $manager->setHttpRequest($this->httpRequest);

        return $manager->retrieveFromCache($key, $static);
    }


    /**
     * can be overridden for custom keys
     *
     * @return string
     */
    protected abstract function getKey($params = null);

}