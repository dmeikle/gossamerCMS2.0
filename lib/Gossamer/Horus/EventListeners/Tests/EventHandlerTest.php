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

use Gossamer\Horus\EventListeners\Event;
use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Horus\EventListeners\EventHandler;
use Gossamer\Pesedget\Database\DatasourceFactory;

/**
 * EventHandlerTest
 *
 * @author Dave Meikle
 */
class EventHandlerTest extends \tests\BaseTest{
    
    public function testAddListener() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $dispatcher = new EventDispatcher($this->getLogger(),$request, $response, 'GET', 'test_server_connect');
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasources());

        $handler = new EventHandler($this->getLogger(), $request, $response);
        $handler->setEventDispatcher($dispatcher);
        $handler->setDatasources(new DatasourceFactory(), $this->getDatasources());
        $listenerConfig = array('listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\TestListener', 'event' => 'request_start');
        $event = new Event('test_add_listener');
        $handler->setListener($listenerConfig);
        $handler->setState('request_start', $event);
        
        $handler->notifyListeners();
        
        $this->assertNotNull($request->getAttribute('result'));
        $this->assertEquals($request->getAttribute('result'), 'TestListener loaded successfully');
    }

}
