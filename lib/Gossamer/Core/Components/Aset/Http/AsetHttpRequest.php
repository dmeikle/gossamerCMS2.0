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
 * Date: 3/5/2017
 * Time: 4:15 PM
 */

namespace Gossamer\Core\Components\Aset\Http;


use Core\Http\HttpRequest;

class AsetHttpRequest extends HttpRequest
{

    protected function getPostedParameters()
    {
        $rp = new \Gossamer\Aset\Http\RequestParameters($this->uri, $this->nodeConfig, parent::getPostedParameters());

        return $rp->getPostParameters(parent::getPostedParameters());
    }


    /**
     * formats the query string into a readable array
     */
    protected function formatQueryString()
    {
        parse_str($_SERVER['QUERY_STRING'], $queryParams);

        $rp = new \Gossamer\Aset\Http\RequestParameters($this->uri, $this->nodeConfig, $queryParams);

        $queryString = $rp->getQueryStringParameters($queryParams);
        if(!is_null($queryString)) {
            $this->queryString = $queryString;
        }

        unset($rp);
    }

    /**
     * removes the base uri and returns only the uri pieces pertinent to the
     * request that are used as request parameters now
     *
     * @param string $filter
     * @param string $uri
     *
     * @return array
     */
    protected function getParams($filter, $uri)
    {

        if (substr($filter, 0, 1) == '/' && substr($uri, 0, 1) != '/') {
            $filter = substr($filter, 1); //knock the preceding '/' if it's not there - varies from server to server
        }
        //array filter knocked off any '0' value, so it has been removed
        //$params = array_filter(explode('/', str_replace($filter, '', $uri)));

        $uri = substr($uri, strlen($filter));

        $params = explode('/', $uri);

        if (current($params) == '') {
            array_shift($params);
        }

        $rp = new \Gossamer\Aset\Http\RequestParameters($this->uri, $this->nodeConfig, $params);

        return $rp->getURIParameters();
    }
}