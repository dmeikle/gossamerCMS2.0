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
 * Time: 5:13 PM
 */

namespace Gossamer\Aset\Http;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\RequestParams;

class AsetHttpRequest extends HttpRequest
{

    private $httpRequest;

    public function __construct(HttpRequest $httpRequest) {

        $this->httpRequest = $httpRequest;

    }

    public function setUriParameters(array $uriParameters) {
        $this->httpRequest->getRequestParams()->setUriParameters($uriParameters);
    }

    public function getQueryStringParameters() {
        return $this->httpRequest->getRequestParams()->getQueryStringParameters();
    }
    public function getUriParameters() {
        return $this->httpRequest->getRequestParams()->getUriParameters();
    }
    
    public function setQueryStringParameters(array $params) {

        return $this->httpRequest->getRequestParams()->setQueryStringParameters($params);
    }

    public function setQueryString(array $queryString) {
        $this->getRequestParams()->setQuerystring($queryString);
    }

    public function getQueryString() {
        return $this->getRequestParams()->getQuerystring();
    }
    public function getMethod(){
        return $this->httpRequest->getRequestParams()->getMethod();
    }

    public function getYmlKey() {
        return $this->httpRequest->getRequestParams()->getYmlKey();
    }

    public function getSiteParams() {
        return $this->httpRequest->getSiteParams();
    }

    public function getRequestParams(){
        return $this->httpRequest->getRequestParams();
    }

    public function setRequestParams(RequestParams $requestParams){
        $this->httpRequest->setRequestParams($requestParams);
    }

    public function setPostParameter($key, $value) {
        $this->httpRequest->setPostParameter($key, $value);
    }

    public function setPost(array $post) {
        $this->httpRequest->getRequestParams()->setPost($post);
    }

    public function getPost() {
        return $this->httpRequest->getRequestParams()->getPost();
    }

    /**
     * @return mixed
     */
    public function getNodeConfig() {
        return $this->httpRequest->getNodeConfig();
    }

    /**
     * @param mixed $nodeConfig
     * @return HttpRequest
     */
    public function setNodeConfig(&$nodeConfig) {
        $this->httpRequest->setNodeConfig($nodeConfig);
    }
}