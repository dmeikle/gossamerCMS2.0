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

class UnSerializeSessionLocallyFilter extends AbstractFilter
{

    private $id;

    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        $session = $this->container->get('CacheManager')->retrieveFromCache($this->getKey(), true);


        if(!is_null($session)) {
            $_SESSION = unserialize($session);

            //we had a key but if there's nothing in the session, we don't want to lose the id
            if(is_null(getSession('CACHE_ID'))) {
                setSession('CACHE_ID', $this->id);
            }

        }

        parent::execute($request, $response, $chain); // TODO: Change the autogenerated stub

    }

    private function getKey() {
        $this->id = getSession('CACHE_ID');

        return $this->filterConfig->get('cacheKey') . DIRECTORY_SEPARATOR . $this->id;
    }
}