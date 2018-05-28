<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Aset\Validation\Validators;

use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * FloatValidator - receives an input and validates only if it holds a value
 *
 * @author	Dave Meikle
 *
 * @copyright 2007 - 2014
 */
class FloatValidator extends AbstractValidator implements FlyweightValidatorInterface {

    public function __construct() {

    }

    public function validate($value) {
        if (filter_var($value, FILTER_VALIDATE_FLOAT)) {
            return true;
        } else {
            return false;
        }
    }

}
