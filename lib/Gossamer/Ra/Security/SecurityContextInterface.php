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
 * SecurityContextInterface
 *
 * @author Dave Meikle
 */
interface SecurityContextInterface {

    public function getToken();

    public function setToken(TokenInterface $token);

    public function isGranted(mixed $attributes, mixed $object = null);
}
