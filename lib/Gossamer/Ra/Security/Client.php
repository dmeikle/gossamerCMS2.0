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
 * Time: 12:32 AM
 */

namespace Gossamer\Ra\Security;


class Client implements ClientInterface {

    protected $id;
    protected $password = null;
    protected $roles = array();
    protected $credentials = 'anonymous';
    protected $ipAddress = null;
    protected $status = null;
    protected $email = null;
    protected $firstname = null;
    protected $lastname = null;

    /**
     *
     * @param array $params
     */
    public function __construct(array $params = array()) {

        foreach($params as $key => $value) {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * accessor
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * accessor
     *
     * @param array $roles
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;
        return $this;
    }

    /**
     * accessor
     *
     * @param string $credentials
     */
    public function setCredentials($credentials) {
        $this->credentials = $credentials;
        return $this;
    }

    /**
     * accessor
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * accessor
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * accessor
     *
     * @return array
     */
    public function getRoles() {
        if (is_array($this->roles)) {
            return $this->roles;
        }

        return explode('|', $this->roles);
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getCredentials() {
        return $this->credentials;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getIpAddress() {
        return $this->ipAddress;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * accessor
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * accessor
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    /**
     * accessor
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    public function toArray() {
        $retval = array();
        foreach(get_object_vars($this) as $key => $property) {

            $retval[$key] = $property;
        }

        return $retval;
    }

    /**
     * @return null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param null $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return null
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param null $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    
    
}