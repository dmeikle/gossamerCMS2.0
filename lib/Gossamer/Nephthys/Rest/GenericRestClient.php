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
 * Date: 3/7/2017
 * Time: 11:17 PM
 */

namespace Gossamer\Nephthys\Rest;


use Psr\Http\Message\RequestInterface;
use Gossamer\Nephthys\Rest\RestInterface;

class GenericRestClient extends \RestClient implements RestInterface
{

    const METHOD_DELETE = 'delete';
    const METHOD_SAVE = 'save';
    const METHOD_PUT = 'put';
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const VERB_LIST = 'list';
    const VERB_DELETE = 'delete';
    const VERB_GET = 'get';
    const VERB_SAVE = 'save';

    private $credentials;

    public function __construct(array $credentials, $options = []) {
        parent::__construct($options);
        $this->credentials = $credentials;
    }

    private function getEntityName($entity) {
        $pieces = explode('\\', get_class($entity));

        return array_pop($pieces);
    }

    public function format_query($parameters, $primary='=', $secondary='&'){

        $query = "";
        foreach($parameters as $key => $values){
            foreach(is_array($values)? $values : [$values] as $value){
                $pair = [$key, urlencode($value)];
                $query .= implode($primary, $pair) . $secondary;
            }
        }
        return rtrim($query, $secondary);
    }


    public function query($requestMethod, $model, $verb, array $params) {
        $api = new GenericRestExtension(array(
            'base_url' => $this->credentials['baseUrl'],
            'format' => $this->credentials['format'],
            'headers' => $this->buildHeaders()
        ));
      

        $result = $api->$requestMethod($model->getTablename() . "/$verb/", $params);
//pr($result);
        if ($result->info->http_code == 200) {
            $decodedResult = $result->decode_response();

            if (is_null($decodedResult) || empty($decodedResult)) {

                return null;
            }
            if (array_key_exists('code', $decodedResult)) {
                $this->handleError($decodedResult);
            }
            //reset the XSS token for next request
            if (isset($decodedResult->AuthorizationToken)) {
                $_SESSION['AuthorizationToken'] = $decodedResult->AuthorizationToken;
            }

            return($decodedResult);
        } elseif ($result->info->http_code == 500) {

            $decodedResult = $result->decode_response();

            if (array_key_exists('code', $decodedResult)) {
                return $this->handleError($decodedResult);
            }
        }elseif($result->error != ''){

            return $this->handleError(array($result->error));
        }
    }

    protected function handleError($result) {
        if (!is_object($result)) {
            //changing behaviour to be more graceful
            if(is_array($result)) {
                return $result;
            }
        }

    }

    /**
     * builds the headers for the request
     *
     * @param type $credentials
     *
     * @return array
     */
    protected function buildHeaders() {

        return $this->credentials['headers'];
    }

    /**
     * gets the credentials to identify ourselves to the API server
     *
     * @param type $ymlKey
     *
     * @return array
     */
    protected function  getCredentials($ymlKey) {

        return $this->credentials['credentials'];
    }

    public function sendAsync(RequestInterface $request, array $options = []) {
        // TODO: Implement sendAsync() method.
    }

    public function send(RequestInterface $request, array $options = []) {
        // TODO: Implement send() method.
    }

    public function requestAsync($method, $uri = '', array $options = []) {
        // TODO: Implement requestAsync() method.
    }

    public function request($method, $uri = '', array $options = []) {
        // TODO: Implement request() method.
    }

    public function getConfig($option = null) {
        // TODO: Implement getConfig() method.
    }
}