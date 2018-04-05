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
 * Date: 3/9/2017
 * Time: 12:31 AM
 */

namespace Gossamer\Ra\Security;



/**
 * the security token used to identify a user on each request
 *
 * @author Dave Meikle
 */
class SecurityToken implements TokenInterface {

    protected $client = null;
    private $identity = null;
    private $roles = null;
    private $isAuthenticated = false;
    private $attributes = array();
    private $ymlKey = null;

    /**
     *
     * @param Client $client
     * @param string $ymlKey
     * @param array $roles
     */
    public function __construct(ClientInterface $client, $ymlKey, array $roles = array()) {
        $this->client = $client;
        $this->ymlKey = $ymlKey;
        $this->roles = $roles;
    }

    public function eraseCredentials() {

    }

    /**
     * accessor
     *
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getIdentity() {
        return $this->identity;
    }

    /**
     * accessor
     *
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * accessor
     *
     * @return boolean
     */
    public function isAuthenticated() {
        return $this->isAuthenticated;
    }

    /**
     * accessor
     *
     * @param string
     */
    public function setIdentity($identity) {
        $this->identity = $identity;
    }

    /**
     * accessor
     *
     * @param type $name
     * @param mixed $value
     */
    public function setAttribute($name, mixed $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * accessor
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
    }

    /**
     * accessor
     *
     * @param boolean $isAuthenticated
     */
    public function setAuthenticated($isAuthenticated) {
        $this->isAuthenticated = $isAuthenticated;
    }

    /**
     * accessor
     *
     * @param Client $client
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * might be used in future to return a serialized string
     */
    public function toString() {

    }

}
