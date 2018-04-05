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
 * Date: 9/6/2017
 * Time: 6:22 PM
 */

namespace Gossamer\Horus\Filters;


class FilterEvents
{
    const FILTER_REQUEST_START = 'request_start';

    const FILTER_REQUEST_FORWARD = 'request_forward';

    const FILTER_ENTRY_POINT = 'entry_point';

    const FILTER_SERIALIZATION = 'request_serialize';
}