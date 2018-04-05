<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Horus\EventListeners\Tests;

use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

/**
 * TestListener
 *
 * @author Dave Meikle
 */
class TestListener extends AbstractListener{
    
    public function on_request_start(Event $event) {
      echo "TestListener loaded successfully\r\n";
        $this->request->setAttribute('result', 'TestListener loaded successfully');
    }
    public function on_request_end(Event $event) {
      
        $this->request->setAttribute('result', 'TestListener loaded successfully');
    }
}
