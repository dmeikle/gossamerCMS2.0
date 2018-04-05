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
 * Time: 9:27 PM
 */

namespace Gossamer\Ra\Security;


/**
 * FormTokenInterface
 *
 * @author Dave Meikle
 */
interface FormTokenInterface
{
    public function setIPAddress($ipAddress);

    public function setTokenString($token);

    public function toString();

    public function getTimestamp();

    public function setCredentials($credentials);

    public function setClientId($id);

    public function generateTokenString();

    public function getTokenString();
} 