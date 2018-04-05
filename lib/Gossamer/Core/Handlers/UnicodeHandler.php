<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace core\handlers;

use Monolog\Logger;

/**
 * converts regular text to unicode.
 * this class was intended for use since we are often saving data to a remote
 * datasource using REST with cURL. Since cURL isn't foreign language friendly
 * I've found the simplest solution is to simply convert text to UTF-8 and send
 * it as hex to the remote source where it doesn't care what it's handling.
 * 
 *
 * @author Dave Meikle
 */
class UnicodeHandler extends BaseHandler {

    private $config = null;
    private $key = null;
    private $action = null;

    const DECODE_FLAG = 'decode';
    const ENCODE_FLAG = 'encode';

    /**
     * 
     * @param Logger $logger
     * @param array $configuration
     */
    public function __construct(Logger $logger, array $configuration) {
        parent::__construct($logger);
        $this->config = $configuration;
    }

    /**
     * 
     * @param array $params
     */
    public function handleRequest($params = array()) {
        $pageConfig = $this->config[__YML_KEY];
        $this->$this->action($params);
    }

    /**
     * 
     * @param string $value
     */
    public function setFlag($value) {
        $this->action = $flag;
    }

    /**
     * 
     * @param type $string
     * @return boolean
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 
     * @param type $object
     * 
     * @return type
     */
    private function convertObjectToArray($object) {
        $retval = array();
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        foreach ($object as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $retval[$key] = $this->convertObjectToArray($value);
            } else {
                $retval[$key] = $value;
            }
        }

        return $retval;
    }

    /**
     * 
     * @param type $parameters
     * 
     * @return type
     */
    public function formatToAsciiAfterReceiving($parameters) {
        //first decouple it from array of objects into an array of arrays
        $array = $this->decoupleToArray($parameters);

        return $this->convertArrayText($array);
    }

    /**
     * 
     * @param type $parameters
     * 
     * @return type
     */
    private function decoupleToArray($parameters) {

        if (is_object($parameters)) {
            $parameters = get_object_vars($parameters);
        }

        return is_array($parameters) ? array_map(__METHOD__, $parameters) : $parameters;
    }

    /**
     * 
     * @param array $parameters
     * @return type
     */
    private function convertArrayText(array $parameters) {
        $retval = array();
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $retval[$key] = $this->convertArrayText($value);
            } else {
                $retval[$key] = $this->hex2ascii($key, $value);
            }
        }

        return $retval;
    }

    /**
     * 
     * @param type $parameters
     * @return type
     */
    public function decode($parameters) {

        $result = $this->formatToAsciiAfterReceiving($parameters);

        return $result;
    }

    /**
     * 
     * @param type $parameters
     * 
     * @return type
     */
    public function encode($parameters) {

        return $this->formatToHexForSending($parameters);
    }

    /**
     * 
     * @param type $parameters
     * 
     * @return array
     */
    private function formatToHexForSending($parameters) {
        $retval = array();
        if (!is_array($parameters)) {

            return $this->ascii2hex($parameters);
        }
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $retval[$key] = $this->formatToHexForSending($value);
            } else {
                $retval[$key] = $this->ascii2hex($key, $value);
            }
        }

        return $retval;
    }

    /**
     * 
     * @param string $key
     * @param string $ascii
     * 
     * @return string
     */
    private function ascii2hex($key, $ascii) {
        if (!in_array($key, $this->config)) {
            return $ascii; //don't bother to convert it - it's not on our list
        }
        $hex = '0x';
        for ($i = 0; $i < strlen($ascii); $i++) {
            $byte = strtoupper(dechex(ord($ascii{$i})));
            $byte = str_repeat('0', 2 - strlen($byte)) . $byte;
            $hex.=$byte . " ";
        }

        return $hex;
    }

    /**
     * 
     * @param string $key
     * @param string $hex
     * 
     * @return string
     */
    private function hex2ascii($key, $hex) {
        if (!in_array($key, $this->config)) {
            return $hex; //don't bother to convert it - it's not on our list
        }
        if (is_object($hex) || substr($hex, 0, 2) !== '0x') {

            return $hex;
        }

        $ascii = '';

        $hex = str_replace(" ", "", substr($hex, 2));
        for ($i = 0; $i < strlen($hex); $i = $i + 2) {
            $ascii.=chr(hexdec(substr($hex, $i, 2)));
        }

        return($ascii);
    }

}
