<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/1/2017
 * Time: 8:21 PM
 */

namespace Gossamer\Core\Http;


class RequestParams
{
    protected $ymlKey;

    protected $server;

    protected $headers;

    protected $post;

    protected $querystring;

    protected $requestParameters = array();

    protected $queryStringParameters;

    protected $uri;

    protected $method;

    protected $layoutType;

    protected $uriParameters;

    protected $siteURL;

    /**
     * @return mixed
     */
    public function getSiteURL() {
        return $this->siteURL;
    }

    /**
     * @param mixed $siteURL
     * @return RequestParams
     */
    public function setSiteURL($siteURL) {
        $this->siteURL = $siteURL;
        return $this;
    }
    

    /**
     * @return mixed
     */
    public function getUriParameters() {
        return $this->uriParameters;
    }

    /**
     * @param mixed $uriParameters
     * @return RequestParams
     */
    public function setUriParameters($uriParameters) {

        $this->uriParameters = $uriParameters;
        return $this;
    }
    

    /**
     * @return mixed
     */
    public function getQueryStringParameters() {

        if (is_null($this->queryStringParameters) && !is_null($this->getQuerystring())) {
            return $this->getQuerystring();
        }

        return $this->queryStringParameters;
    }

    /**
     * @param mixed $queryStringParameters
     */
    public function setQueryStringParameters($queryStringParameters) {
        $this->queryStringParameters = $queryStringParameters;
    }

    /**
     * @param mixed $queryStringParameters
     */
    public function getQueryStringParameter($key) {
        if(array_key_exists($key, $this->queryStringParameters)) {
            return $this->queryStringParameters[$key];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getRequestParameters() {
        return $this->requestParameters;
    }

    /**
     * @param mixed $requestParameters
     * @return RequestParams
     */
    public function setRequestParameters($requestParameters) {
        $this->requestParameters = $requestParameters;
        return $this;
    }

    public function setPostParameter($key, $value) {
        $this->post[$key] = $value;
    }
    
    /**
     * @return mixed
     */
    public function getLayoutType() {
        return $this->layoutType;
    }

    /**
     * @param mixed $layoutType
     * @return RequestParams
     */
    public function setLayoutType($layoutType) {
        $this->layoutType = $layoutType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return RequestParams
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYmlKey() {
        return $this->ymlKey;
    }

    /**
     * @param mixed $ymlKey
     * @return RequestParams
     */
    public function setYmlKey($ymlKey) {
        $this->ymlKey = $ymlKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServer($key = null) {
        if (!is_null($key) && array_key_exists($key, $this->server)) {
            return $this->server[$key];
        }

        return $this->server;
    }

    /**
     * @param mixed $server
     * @return RequestParams
     */
    public function setServer($server) {
        $this->server = $server;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     * @return RequestParams
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @param mixed $post
     * @return RequestParams
     */
    public function setPost($post) {
        $this->post = $post;
        return $this;
    }

    public function getPostParameter($key) {
        if(array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getQuerystring($asArray = true) {
        if (!$asArray) {
            return http_build_query($this->querystring);
        }

        return $this->querystring;
    }

    /**
     * @param mixed $querystring
     * @return RequestParams
     */
    public function setQuerystring($querystring) {

        $this->setUri(key($querystring));
        array_shift($querystring);
        $this->querystring = $querystring;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     * @return RequestParams
     */
    public function setUri($uri) {

        $this->uri = $uri;
        return $this;
    }
    

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
    }

    public function getUrlSegments()
    {
        $segments = explode('/', $this->uri);
        if(count($segments) > 2) {
            array_pop($segments);
        }
        return $segments;
    }


}