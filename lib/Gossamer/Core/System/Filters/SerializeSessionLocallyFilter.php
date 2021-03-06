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
 * Date: 3/18/2018
 * Time: 4:22 PM
 */

namespace Gossamer\Core\System\Filters;


use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;

class SerializeSessionLocallyFilter extends AbstractFilter
{
    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        $cacheManager = $this->container->get('CacheManager');

        $cacheManager->saveToCache($this->getKey(),serialize($_SESSION));
        session_destroy();
        parent::execute($request, $response, $chain); // TODO: Change the autogenerated stub

    }

    private function getKey() {
        $id = getSession('CACHE_ID');

        return $this->filterConfig->get('cacheKey') . DIRECTORY_SEPARATOR . $id;
    }
}