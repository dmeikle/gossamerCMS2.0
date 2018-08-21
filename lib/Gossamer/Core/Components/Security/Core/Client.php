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
 * Date: 3/26/2017
 * Time: 4:00 PM
 */

namespace Gossamer\Core\Components\Security\Core;



use Gossamer\Core\Components\Security\Core\ClientInterface;

/**
 * Client class used for storing user context data
 *
 * @author Dave Meikle
 */
class Client extends \Gossamer\Ra\Security\Client implements ClientInterface {

    
    protected $memberID;
    protected $memberPrefix;
    protected $username;

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
     * @return mixed
     */
    public function getMemberPrefix() {
        return $this->memberPrefix;
    }

    /**
     * @param mixed $memberPrefix
     * @return Client
     */
    public function setMemberPrefix($memberPrefix) {
        $this->memberPrefix = $memberPrefix;
        return $this;
    }

    
    /**
     * @return mixed
     */
    public function getMemberID() {
        return $this->memberID;
    }

    /**
     * @param mixed $memberID
     * @return Client
     */
    public function setMemberID($memberID) {
        $this->memberID = $memberID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    
}
