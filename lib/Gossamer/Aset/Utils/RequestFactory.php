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
use Gossamer\Aset\Exceptions\StrictVariableEnforcementException;
use Gossamer\Aset\Http\AsetHttpRequest;
use Gossamer\Horus\Http\HttpRequest;

class RequestFactory
{
    private $caster;
    private $configParameters;
    private $siteConfig;
    
    const URI = 'uri';
    const POST = 'post';
    const QUERY = 'query';
    
    const ENFORCEMENT_STRICT = 'strict';
    const ENFORCEMENT_LOOSE = 'loose';
    const ENFORCEMENT_DEBUG = 'debug';

    public function getHttpRequest(HttpRequest &$httpRequest) {

        $this->siteConfig = $httpRequest->getSiteParams()->getSiteConfig();
        $this->caster = new ParamTypeCaster($this->siteConfig);
        $this->configParameters = $httpRequest->getNodeConfig();

        $temp = array_shift($this->configParameters);

        $parameters = array();
        if(isset($temp['parameters'])) {
            $parameters = $temp['parameters'];
        }

        $asetHttpRequest = new AsetHttpRequest($httpRequest);
        $asetHttpRequest->setQueryStringParameters($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getQuerystring(), self::QUERY));
     //TODO: this overwrites querystringparams
      //  $asetHttpRequest->setQueryString($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getQuerystring(), self::QUERY));
        $asetHttpRequest->setUriParameters($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getUriParameters(), self::URI));

        $asetHttpRequest->setPost($this->getCastParameters($parameters, $httpRequest->getRequestParams()->getPost(), self::POST));
        //$asetHttpRequest->setPost($httpRequest->getRequestParams()->getPost(), self::POST);
      
        return $asetHttpRequest;
    }

    private function getCastParameters(array $params, array $values = null, $type) {

        $retval = array();
        if(is_null($values)) {

            return $retval;
        }

        //in case it's not specified in the parameters.yml file to enforce this
        //just let it through since enforcing is not requested
        if(!isset($params[$type])) {           

            return $values;
        }

        foreach($params[$type] as $index => $param) {

            if(isset($values[$param['key']])) {
                $retval[$param['key']] = $this->caster->cast($param, $values[$param['key']], $param['key']);
            }
        }

        return $retval;
    }
}