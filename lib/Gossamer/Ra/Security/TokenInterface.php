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
 * Time: 8:45 PM
 */

namespace Gossamer\Ra\Security;


/**
 * TokenInterface
 *
 * @author Dave Meikle
 */
interface TokenInterface {

    public function toString();

    public function getRoles();

    public function getClient();

    public function setClient(ClientInterface $client);

    public function getIdentity();

    public function isAuthenticated();

    public function setAuthenticated($isAuthenticated);

    public function setAttribute($name, mixed $value);

    public function getAttributes();

    public function setAttributes(array $attributes);

    public function eraseCredentials();
}
