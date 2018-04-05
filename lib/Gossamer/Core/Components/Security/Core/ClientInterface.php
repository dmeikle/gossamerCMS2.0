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


/**
 * ClientInterface
 *
 * @author Dave Meikle
 */
interface ClientInterface {

    public function setPassword($password);

    public function setRoles(array $roles);

    public function setCredentials($credentials);

    public function setIpAddress($ipAddress);

    public function getPassword();

    public function getRoles();

    public function getCredentials();

    public function getIpAddress();

    public function setStatus($status);

    public function getStatus();

    public function getId();
}
