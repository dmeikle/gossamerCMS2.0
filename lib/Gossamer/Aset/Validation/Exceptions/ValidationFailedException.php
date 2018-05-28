<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Aset\Validation\Exceptions;

/**
 * ValidationFailedException
 *
 * @author Dave Meikle
 */
class ValidationFailedException extends \Exception{
    
    public function __construct($message = 'Validation Failed', $code = 800, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
