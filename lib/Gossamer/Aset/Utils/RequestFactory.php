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
 * Date: 5/26/2018
 * Time: 5:42 PM
 */

namespace Gossamer\Aset\Utils;


use Gossamer\Aset\Casting\ParamTypeCaster;
use Gossamer\Aset\Http\AsetHttpRequest;
use Gossamer\Horus\Http\HttpRequest;

class RequestFactory
{
    private $caster;
    private $configParameters;


    public function getHttpRequest(HttpRequest &$httpRequest) {
        $this->caster = new ParamTypeCaster($httpRequest->getSiteParams()->getSiteConfig());
        $this->configParameters = $httpRequest->getNodeConfig();
        $temp = array_shift($this->configParameters);
        $parameters = $temp['parameters'];

        $asetHttpRequest = new AsetHttpRequest($httpRequest);
        $asetHttpRequest->setQueryString($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getQuerystring()));
        $asetHttpRequest->setUriParameters($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getUriParameters()));
        //$asetHttpRequest->setPost($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getPost()));
        $asetHttpRequest->setPost($httpRequest->getRequestParams()->getPost());
        
        return $asetHttpRequest;
    }

    private function getCastParameters(array $params, array $values) {
        $retval = array();

        foreach($params as $index => $param) {
            if(array_key_exists($param['key'], $values)) {
                $retval[$param['key']] = $this->caster->cast($param, $values[$param['key']], $param['key']);
            }
        }

        return $retval;
    }
}