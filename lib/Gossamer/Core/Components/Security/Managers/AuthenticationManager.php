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
 * Date: 3/15/2017
 * Time: 6:50 PM
 */

namespace Gossamer\Core\Components\Security\Managers;


use Gossamer\Core\Services\ParametersInterface;
use Gossamer\Ra\Security\Client;
use Gossamer\Ra\Security\SecurityToken;

class AuthenticationManager extends \Gossamer\Ra\Security\Managers\AuthenticationManager implements ParametersInterface
{

    use \Gossamer\Set\Utils\ContainerTrait;
    /**
     *
     * @return \Gossamer\Ra\Security\Client
     */
    public function getClient() {
        $post = ($this->request->getRequestParams()->getMethod() == 'POST') ? $this->request->getRequestParams()->getPost() : null;
        $client = new Client($post);
        $client->setRoles(array('ROLE_ANONYMOUS_USER'));
        $client->setCredentials($this->getClientHeaderCredentials());

        return $client;
    }

    /**
     * retrieves a list of credentials (IS_ADMINISTRATOR|IS_ANONYMOUS...)
     *
     * @return array(credentials)|null
     */
    protected function getClientHeaderCredentials() {
        $headers = getallheaders();
        if (array_key_exists('credentials', $headers)) {
            return $headers['credentials'];
        }

        return null;
    }
    /**
     * generates a default token
     *
     * @return SecurityToken
     */
    public function generateEmptyToken($session = array()) {

        if(is_null($session)) {
            $session = array();
        }

        $token = false;
        if(array_key_exists('_security_secured_area', $session)) {
            $token = unserialize($session['_security_secured_area']);
        }


        if (!$token) {
            return $this->generateNewToken();
        }

        return $token;
    }

    protected function getSession() {
      
        return  $_SESSION;
    }


    /**
     * @param Client|null $client
     * @return SecurityToken
     */
    public function generateNewToken(Client $client = null) {
        if(is_null($client)) {
            $client = $this->getClient();
        }

        $token = new SecurityToken($client, $this->request->getRequestParams()->getYmlKey(), $client->getRoles());

        return $token;
    }

    public function setParameters(array $parameters) {
        // TODO: Implement setParameters() method.
    }
}