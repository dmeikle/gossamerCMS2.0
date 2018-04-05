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
 * Date: 3/3/2017
 * Time: 12:56 AM
 */

namespace Gossamer\Core\Components\Security\Filters;


use components\security\documents\ServerAuthenticationToken;
use Gossamer\Caching\CacheManager;
use Gossamer\Core\Components\Security\Exceptions\InvalidServerIDException;
use Gossamer\Core\Components\Security\Exceptions\UnauthorizedAccessException;
use Gossamer\Horus\Filters\AbstractFilter;
use Gossamer\Horus\Filters\FilterChain;
use Gossamer\Horus\Http\HttpRequest; use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Horus\Http\HttpRequest;

class CheckServerCredentialsFilter extends AbstractFilter
{
    use \Gossamer\Core\Components\Security\Traits\LoadSystemSaltTrait;



    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {

        $headers = getallheaders();


        $result = $this->checkHeaders($headers);
       // throw new \Exception('test the error response', 405);
        return $chain->execute($request, $response, $chain);
    }

    private function checkHeaders(array $headers) {

        // file_put_contents(__DEBUG_OUTPUT_PATH, print_r($headers, true) . "\r\n", FILE_APPEND);
        if (!array_key_exists('serverName', $headers) || strlen($headers['serverName']) < 1) {

            throw new InvalidServerIDException('server identification missing from Headers');
        }

        if (!$this->checkServer($headers['serverName'], $this->httpRequest->getRequestParams()->getClientIPAddress())) {


            throw new UnauthorizedAccessException($this->httpRequest->getRequestParams()->getClientIPAddress() . ' is not authorized');
        }
    }


    private function checkServer($authToken, $ipAddress)
    {


        $cachedToken = $this->retrieveFromCache($authToken, $ipAddress);

        if ($cachedToken != false) {
            $this->httpRequest->setAttribute('CLIENT_DATABASE', $cachedToken['dbName']);

            return true;
        }

        //we didn't find it in cache - do a lookup
        $token = new ServerAuthenticationToken();

        $cmd = $this->getCommandContext();

        // file_put_contents('/var/www/db-repo/logs/save-test.log', print_r(array('token' => $authToken, 'ipAddress' =>$ipAddress), true) . "\r\n", FILE_APPEND);
        $results = $cmd->execute(array('serverName' => $authToken, 'ipAddress' => $ipAddress, 'datasource' => $this->filterConfig->get('datasource')), null);

        if (is_null($results) || count($results) == 0) {
            throw new UnauthorizedAccessException("Null Result - Server not found", 1);
        }

        $tokenResult = $results['ServerAuthenticationToken'];
        //check to see if the token is expired - only used if we have a licensing agreement that expires
        // if($result['expirationTime'] < time()) {
        // throw new UnauthorizedAccessException();
        // }

        if (!is_array($tokenResult) || count($tokenResult) < 1) {
             throw new UnauthorizedAccessException("Token Result - Server not found", 1);
        }

        if ($this->checkTokensMatch($authToken, $tokenResult['token'])) {

            $this->httpRequest->setAttribute('CLIENT_DATABASE', $tokenResult['dbName']);
            $this->saveToCache($tokenResult, $ipAddress);

            return true;
        }

        return false;
    }

    private function saveToCache($token, $ipAddress)
    {
        $cacheManager = $this->container->get('CacheManager');
        $ipAddress = str_replace('.', '_', $ipAddress);

        $cacheManager->saveToCache('ServerAuthenticationTokens_' . $ipAddress, $token);
    }

    public function retrieveFromCache($authToken, $ipAddress)
    {

        $cacheManager = $this->container->get('CacheManager');

        $ipAddress = str_replace('.', '_', $ipAddress);

        $token = $cacheManager->retrieveFromCache('ServerAuthenticationTokens_' . $ipAddress);
        unset($cacheManager);
        if (!is_array($token) || !array_key_exists('token', $token)) {
            return false;
        }

        if ($this->checkTokensMatch($authToken, $token['token'])) {

            return $token;
        }

        return false;
    }

    private function checkTokensMatch($serverName, $token)
    {

        $salt = $this->getSalt($this->httpRequest->getSiteParams());
        error_log(crypt($this->httpRequest->getRequestParams()->getClientIPAddress() . '_' . $serverName . '_' . $salt, $token));
        return crypt($this->httpRequest->getRequestParams()->getClientIPAddress() . '_' . $serverName . '_' . $salt, $token) == $token;
    }

    private function getCommandContext(){
        $commandClass = $this->filterConfig->get('command');
        $entityName = $this->filterConfig->get('entity');

        $cmd = new $commandClass(new $entityName(), $this->container->get('Logger'), $this->httpRequest, $this->httpResponse, $this->container->get('EntityManager'));
        $cmd->setContainer($this->container);

        return $cmd;
    }
}