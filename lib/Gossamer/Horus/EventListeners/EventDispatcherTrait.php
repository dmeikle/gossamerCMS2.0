<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Horus\EventListeners;

use Gossamer\Horus\EventListeners\EventDispatcher;

/**
 * EventDispatcherTrait
 *
 * @author Dave Meikle
 */
trait EventDispatcherTrait {
    
    protected $eventDispatcher;
    
    function getEventDispatcher() {
        return $this->eventDispatcher;
    }

    function setEventDispatcher(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }


}
