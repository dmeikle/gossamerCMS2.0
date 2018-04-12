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
 * Time: 8:50 PM
 */

namespace Gossamer\Core\Components\Caching\Filters;


use Gossamer\Caching\CacheManager;
use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;


class SaveCachedResultFilter extends AbstractFilter
{
    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    /** 
     * @param HttpRequest &$request
     * @param HttpInterface $response
     * @param FilterChain $chain
     * @return bool
     */
    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        if(!$request instanceof HttpRequest) {
            throw new \Exception('Must be an instance of HttpRequest');
        }
        $cacheKey = $this->getCacheKey($request);
        if($cacheKey !== false) {
            $result = false;
            try {
                $result = $response->getAttribute('result');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            if ($result !== false) {
                $this->container->get('CacheManager')->saveToCache('pages' . DIRECTORY_SEPARATOR . $this->getCacheKey($request), $result, true);
            }
        }

        //not a cached page - continue with filter chain
        try {
            return $chain->execute($request, $response, $chain);
        } catch (\Exception $e) {

        }

    }

    protected function getCacheKey(HttpRequest $httpRequest) {
        $nodeConfig = $httpRequest->getNodeConfig();
        //some configurations come unbound from the retaining yml key
        if(!array_key_exists($httpRequest->getYmlKey(), $nodeConfig)) {
            if (array_key_exists('cachable', $nodeConfig) && $nodeConfig['cachable'] == true) {

                return md5($httpRequest->getRequestParams()->getUri() . $httpRequest->getRequestParams()->getQuerystring(false));
            }
            return false;
        }
        //some configurations have a yml key containing them
        if (array_key_exists('cachable', $nodeConfig[$httpRequest->getYmlKey()]) && $nodeConfig[$httpRequest->getYmlKey()]['cachable'] == true) {

            return md5($httpRequest->getRequestParams()->getUri() . $httpRequest->getRequestParams()->getQuerystring(false));
        }

        return false;
    }



    protected function getKey(HttpRequest $request) {
        return md5($request->getRequestParams()->getServer('REQUEST_URI') . $request->getRequestParams()->getQueryString(false));
    }

}