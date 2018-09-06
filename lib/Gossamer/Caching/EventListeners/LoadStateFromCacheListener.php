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
 * Date: 8/28/2018
 * Time: 10:32 AM
 */

namespace Gossamer\Caching\EventListeners;


use Gossamer\Caching\CacheManager;
use Gossamer\Core\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class LoadStateFromCacheListener extends AbstractListener
{
    public function on_entry_point(Event &$event) {

        $session = getSession();
        if(!array_key_exists('CACHE_ID', $session)) {
          //  throw new KeyNotSetException('CACHE_ID missing from session object');
        }

//    /    $this->cache($session['CACHE_ID'], $session);
    }


    private function cache ($id, $buffer) {

        $config = $this->request->getSiteParams()->getSiteConfig();
        if(!isset($config['system']['cache']['directory'])) {
            throw new KeyNotSetException('cache directory not specified in site config');
        }
        $directory = $config['system']['cache']['directory'];

        $this->cacheManager->saveToCache($directory . DIRECTORY_SEPARATOR . $id, $buffer);

        return true;
    }

}