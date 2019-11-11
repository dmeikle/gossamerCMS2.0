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
 * Date: 9/19/2017
 * Time: 5:26 PM
 */

namespace Gossamer\Core\Filters;


use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;

class ListAllFilter extends AbstractFilter
{
    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {
        $this->httpRequest = $request;

        $params = array(
        );
        $modelName = $this->filterConfig->get('model');

        $list = $this->getEntityManager()->getConnection($this->filterConfig->get('datasource'))->query(self::METHOD_GET, new $modelName($request, $response, $this->container->get('Logger')), 'listminimal', $params);

        //map it to a new unique key
        $response->setAttribute($this->filterConfig->get('key'), $list[$this->filterConfig->get('dbkey')]);

        try {
            return $chain->execute($request, $response, $chain);
        } catch (\Exception $e) {

        }
    }
}