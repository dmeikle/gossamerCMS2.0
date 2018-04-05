<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Horus\Http;

/**
 * Request
 *
 * @author Dave Meikle
 */
abstract class Request implements HttpInterface{
    
    private $attributes = array();

    protected $headers = array();
    
    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    public function getAttribute($key) {
        if(!array_key_exists($key, $this->attributes)) {
            return null;
        }
        
        return $this->attributes[$key];
    }


    public function getAttributes() {
        return $this->attributes;
    }

    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }
}
