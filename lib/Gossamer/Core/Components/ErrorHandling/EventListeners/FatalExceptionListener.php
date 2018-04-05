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
 * Date: 3/5/2017
 * Time: 3:53 PM
 */

namespace Gossamer\Core\Components\ErrorHandling\EventListeners;


use Gossamer\Core\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

class FatalExceptionListener extends AbstractListener
{

    public function on_error_occurred(Event $event) {
        echo debug_backtrace()[0]['function']."<br>\r\n";
        echo debug_backtrace()[1]['function']."<br>\r\n";
        echo debug_backtrace()[2]['function']."<br>\r\n";
        echo debug_backtrace()[3]['function']."<br>\r\n";
        echo debug_backtrace()[4]['function']."<br>\r\n";
        echo debug_backtrace()[5]['function']."<br>\r\n";
        echo debug_backtrace()[6]['function']."<br>\r\n";
      //  pr(debug_backtrace()[2]);
        die('error - in exception listener');
    }
}