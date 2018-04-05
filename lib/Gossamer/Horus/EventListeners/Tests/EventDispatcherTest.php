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
use Gossamer\Pesedget\Database\DatasourceFactory;

/**
 * EventDispatcherTest
 *
 * @author Dave Meikle
 */
class EventDispatcherTest extends \tests\BaseTest {
    
    public function testAddListener() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $values = array();
        $event = new Event('test_add_listener');
        
        $dispatcher = new EventDispatcher($this->getLogger(),$request, $response, 'GET', 'test_add_listener');
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasources());
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('all', 'request_start', $event);
              
//        $this->assertNotNull($request->getAttribute('result'));
//        $this->assertEquals($request->getAttribute('result'), 'TestListener loaded successfully');
    }
    
    /**
     * @group initiate
     */
    public function testServerInitiate() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $event = new Event('test_server_initiate');
        $dispatcher = new EventDispatcher($this->getLogger(),$request, $response, 'GET', 'test_server_initiate');
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasources());
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('server', 'server_initiate', $event);

//        $this->assertNotNull($request->getAttribute('result_on_server_initiate'));
      
    }
    
    /**
     * @group startup
     */
    public function testServerStartup() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $event = new Event('test_server_startup');
        $dispatcher = new EventDispatcher($this->getLogger(),$request, $response, 'GET', 'test_server_startup');
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasources());
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('server', 'server_startup', $event);

       // $this->assertNotNull($request->getAttribute('result_on_server_startup'));
      
    }
    
    /**
     * @group connect
     */
    public function testServerConnect() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $event = new Event('test_server_connect');
        $dispatcher = new EventDispatcher($this->getLogger(),$request, $response, 'GET', 'test_server_connect');
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasources());
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('server', 'client_server_connect', $event);

     //   $this->assertNotNull($request->getAttribute('result_on_client_server_connect'));
      
    }
    
    
    private function getListenerConfig() {
        return array( 
            'all' => array(
                'listeners' => array (
                    array(
                        'event' => 'request_start',
                        'listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\TestListener'
                    ),
                    array(
                        'event' => 'request_end',
                        'listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\TestListener'
                    )
                )
            ),
            'server' => array(  
                'listeners' => array(
                    array(
                        'event' => 'client_server_connect',
                        'listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\ServerEventListener'
                    ),
                    array(
                        'event' => 'server_initiate',
                        'listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\ServerEventListener'
                    ),
                    array(
                        'event' => 'server_startup',
                        'listener' => 'Gossamer\\Horus\\EventListeners\\Tests\\ServerEventListener'
                    )
                )
            )
        );
    }
}
//listeners:
//        
//        - { 'event': 'request_start', 'listener': 'components\staff\listeners\LoadEmergencyContactsListener', 'datasource': 'datasource1' }
//        - { 'event': 'request_start', 'listener': 'core\eventlisteners\LoadListListener', 'datasource': 'datasource1', 'class': 'components\geography\models\ProvinceModel', 'cacheKey': 'Provinces' }
    