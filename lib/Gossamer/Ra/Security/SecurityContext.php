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
 * Time: 8:44 PM
 */

namespace Gossamer\Ra\Security;


/**
 * stores the token for the current user
 *
 * @author Dave Meikle
 */
class SecurityContext implements SecurityContextInterface {

    private $token = null;


    /**
     * @return TokenInterface
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param mixed $attributes
     * @param mixed|null $object
     */
    final public function isGranted(mixed $attributes, mixed $object = null) {

    }

    /**
     * @param TokenInterface $token
     */
    public function setToken(TokenInterface $token) {
        $this->token = $token;
    }

}