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
 * Date: 10/28/2017
 * Time: 7:25 PM
 */

namespace Gossamer\Horus\Filters;


use Gossamer\Horus\Http\HttpRequest; use Gossamer\Horus\Http\HttpResponse;

class ListAllCachableFilter extends AbstractCachableFilter
{
    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {
        $list = $this->retrieveFromCache();
        if($list === false) {
            $this->httpRequest = $request;

            $params = $this->filterConfig->get('params');

            if (is_null($params)) {
                $params = array();
            }
            $params['isActive'] = '1';

            $modelName = $this->filterConfig->get('model');
            $model = new $modelName($request, $response, $this->container->get('Logger'));

            $list = $this->getEntityManager()->getConnection('datasource1')->query(self::METHOD_GET, $model, 'listminimal', $params);

            $this->saveToCache($list);
        }

        $response->setAttribute($this->filterConfig->get('key'), $list[ $this->filterConfig->get('responseKey')]);

        try {
            return $chain->execute($request, $response, $chain);
        } catch (\Exception $e) {

        }
    }
}