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
 * Date: 3/1/2017
 * Time: 9:07 PM
 */

namespace Gossamer\Core\System;


class KernelEvents
{
    const BOOTSTRAP = 'bootstrap';


    const ENTRY_POINT = 'entry_point';

    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching
     */
    const REQUEST_START = 'request_start';

    const REQUEST_END = 'request_end';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears
     */
    const EXCEPTION = 'exception';

    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance
     */
    const VIEW = 'view';

    /**
     * The CONTROLLER event occurs once a controller was found for
     * handling a request
     */
    const CONTROLLER = 'controller';

    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request
     */
    const RESPONSE_START = 'response_start';

    const RESPONSE_END = 'response_end';

    /**
     * The TERMINATE event occurs once a response was sent
     */
    const TERMINATE = 'terminate';

    const ERROR_OCCURRED = 'error_occurred';
}