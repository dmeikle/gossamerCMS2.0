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
 * Date: 2/3/2018
 * Time: 9:23 PM
 */

namespace Gossamer\Ra\Security\Traits; 


use Gossamer\Core\Components\Security\Core\Client;
use Gossamer\Ra\Security\FormToken;

trait FormTokenTrait
{
    /**
     *
     * @return FormToken
     */
    protected function getDefaultToken() {
        $client = $this->getClient();
        return new FormToken($client);
    }

    /**
     *
     * @return \core\components\security\core\Client
     */
    protected function getClient() {
        $token = $this->getSecurityContextToken();
        $client = null;
        if (is_null($token) || !$token) {
            $client = new Client();
            $client->setIpAddress($this->getClientIPAddress());
            $client->setCredentials('ANONYMOUS_USER');
        } else {
            $client = $token->getClient();
        }
        return $client;
    }

    /**
     *
     * @return \core\components\security\core\FormToken
     */
    protected function getSecurityContextToken() {
        $token = unserialize(getSession('_security_secured_area'));
        return $token;
    }

    /**
     *
     * @return string
     */
    protected function getClientIPAddress() {
        //Just get the headers if we can or else use the SERVER global
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {
            $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        return $the_ip;
    }
}