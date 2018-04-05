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
 * Date: 2/8/2018
 * Time: 8:16 PM
 */

namespace Gossamer\Ra\JWT;


class Token
{
    private $values;

    public function __construct(array $values) {
        $this.$values = $values;
    }

    public function toJwtString() {
        
    }
}