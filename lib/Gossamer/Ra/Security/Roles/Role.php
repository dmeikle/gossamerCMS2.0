<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Ra\Security\Roles;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/23/2018
 * Time: 12:37 PM
 */
class Role
{

    private $role;

    /**
     * Role constructor.
     * @param string $role
     */
    public function __construct(string $role) {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole() {
        return $this->role;
    }
}