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
 * Date: 3/6/2017
 * Time: 9:54 PM
 */

namespace Gossamer\Core\Components\Caching\EventListeners;


use Gossamer\Caching\CacheManager;
use Gossamer\Core\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class WriteResultToCacheListener extends AbstractListener
{

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;


    public function on_response_end(Event $event) {

        $nodeConfig = $this->httpRequest->getNodeConfig();

        //only cache if required
        if(!array_key_exists('cached', $nodeConfig) || $nodeConfig['cached'] != '1') {

            return;
        }
    
        $params = $event->getParams();

        $cacheManager = new CacheManager();
        $cacheManager->setHttpRequest($this->httpRequest);

        $cacheManager->saveToCache($this->getKey(), $params);
        unset($cacheManager);
    }

    protected function getKey() {
        return md5($this->httpRequest->getRequestParams()->getServer('REQUEST_URI') . $this->httpRequest->getRequestParams()->getQueryString(false));
        //return hash($salt, $this->request->getRequestParams()->getUri());
    }
}