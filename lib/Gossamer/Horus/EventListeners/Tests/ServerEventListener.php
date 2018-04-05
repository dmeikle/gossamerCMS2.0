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
 * ServerEventListener
 *
 * @author Dave Meikle
 */
class ServerEventListener extends AbstractListener {
    
    public function on_server_initiate(Event &$event) {
        $this->request->setAttribute('result_on_server_initiate', 'successful call');
        echo '>>Ticker Server initiating startup on ' . $event->getParam('host') . ':' . $event->getParam('port') . "\r\n";
        $this->logger->addInfo('Ticker Server initiating startup on ' . $event->getParam('host') . ':' . $event->getParam('port'));
    }
    
    public function on_server_startup(Event &$event) {
        $this->request->setAttribute('result_on_server_startup', 'successful call');
        echo '>> Ticker Server successfully started on ' . $event->getParam('host') . ':' . $event->getParam('port') . "\r\n";
        $this->logger->addInfo('Ticker Server successfully started');
    }
    
    /**
     * used to check authorization token against an allowable list
     * 
     * @param Event $event
     */
    public function on_client_server_connect(Event &$event) {
        $this->request->setAttribute('result_on_client_server_connect', 'successful call');
        $this->request->setAttribute('result', 'successful call');
      
        echo "here is connection\r\n";
        
    }
}
