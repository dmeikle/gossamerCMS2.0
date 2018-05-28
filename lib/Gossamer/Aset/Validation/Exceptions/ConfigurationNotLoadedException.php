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
 * ConfigurationNotLoadedException
 *
 * @author Dave Meikle
 */
class ConfigurationNotLoadedException extends \Exception {

    public function __construct() {
        parent::__construct('Validation configuration array not found', 804);
    }

}
