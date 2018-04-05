<?php

namespace Gossamer\Horus\EventListeners;

class Event
{
    private $eventName = null;
    
    private $params = null;
    
    
    public function __construct($eventName, array $params = null) {
        $this->eventName = $eventName;
        $this->params = $params;
    }
    
    public function getEventName() {
        return $this->eventName;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function getParam($key) {
        if(!is_null($this->params) && array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        
        return null;
    }
    
    public function setParam($key, $params) {
        $this->params[$key] = $params;
    }
}
