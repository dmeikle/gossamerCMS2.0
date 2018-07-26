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
 * Date: 6/8/2018
 * Time: 1:03 PM
 */

namespace Gossamer\Aset\Exceptions;


class StrictVariableEnforcementException extends \Exception
{

    public function __construct($message, $code, Exception $previous) {
        parent::__construct('Unexpected parameter passed', $code, $previous);
    }
}