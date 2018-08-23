<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/** *
 * Author: dave
 * Date: 9/13/2016
 * Time: 1:47 PM
 */

namespace Gossamer\Aset\Casting;


use Gossamer\Aset\Exceptions\InvalidDataTypeException;

class ParamTypeCaster
{
    private $castTypes = array();

    private $siteConfig;

    public function __construct(array $siteConfig) {
        $this->siteConfig = $siteConfig;
    }

    public function cast(array $parameter, $value, $key) {

        $mask = array_key_exists('mask', $parameter) ? $parameter['mask'] : null;
        if($this->siteConfig['security']['variables']['paranoid'] == '1') {
            if(!$this->checkDataType($parameter['type'], $value)) {
                throw new InvalidDataTypeException($key .' is an invalid data type');
            }
        }

        return $this->getType($parameter['type'], $value, $mask);
    }

    /**
     * @param $type
     * @param $value
     * @param null $mask
     * @return mixed
     *
     * converts the value into the specified datatype
     */
    private function getType($type, $value, $mask = null) {
        switch($type) {
            case 'float':
                return floatval($value);
            case 'int':
                return intval($value);
            case 'bool':
            case 'boolean':
                if($value == '1' || $value =='0') {
                    return $value;
                }
                if(strtolower($value) =='true' || strtolower($value) == 'false') {
                    return $value;
                }

                return boolval($value);
            case 'string':
                if(!is_null($mask)) {

                    return preg_replace($mask, '', $value);
                }

            return $value;
        }

    }

    /**
     * @param $dataType
     * @param $value
     * @return bool
     *
     * checks to see whether the value passed in is the correct datatype
     */
    private function checkDataType($dataType, $value) {
        switch($dataType) {
            case 'float':
                return is_float($value);
            case 'int':
                return ((int) $value == $value);
            case 'bool':
            case 'boolean':
                if($value == '1' || $value =='0') {
                    return true;
                }
                if(strtolower($value) =='true' || strtolower($value) == 'false') {
                    return true;
                }

                return is_bool($value);
            case 'string':
                return true;
        }
    }
}