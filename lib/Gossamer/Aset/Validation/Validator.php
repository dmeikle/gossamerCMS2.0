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
 * Date: 5/27/2018
 * Time: 2:38 PM
 */

namespace Gossamer\Aset\Validation;


use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Aset\Validation\Factory\ValidatorCommandFactory;

class Validator
{
    protected $logger;

    protected $config;

    protected $factory;

    public function __construct(array $config, LoggingInterface $logger) {
        $this->logger = $logger;
        $this->config = $config;
    }

    private function getFactory() {
        if(is_null($this->factory)) {
            $this->factory = new ValidatorCommandFactory();
        }

        return $this->factory;
    }

    /**
     * iterate the configuration looking for fields that need validating
     * @param array $postedParams
     * @param bool $keepNestedResult
     */
    public function validateRequest(array $postedParams, $keepNestedResult = false) {
        $retval = array();

        if(array_key_exists('fields', $this->config)) {

            foreach ($this->config['fields'] as $key => $fieldConfig) {

                $validationResult = $this->validateField($key, $fieldConfig, $postedParams);
                if ($validationResult !== true) {
                    $retval[$key] = $validationResult;
                }
            }
        }

        if(count($retval) == 0) {
            return true;
        }

        return $retval;
    }


    private function validateField($key, array &$fieldConfig, array $postedParams, $parent = null) {

        //drill down if this is configured to expect a nested object value
        if(!is_null($parent) && array_key_exists($parent, $postedParams)) {
            $postedParams = $postedParams[$parent];
        }

        //break it down further if there's a '.'
        //eg: BillingAddress.firstname becomes [BillingAddress][firstname]
        if(strpos($key, '.') !== false) {
            $pieces = explode('.', $key);
            //key is the last item
            $key = array_pop($pieces);
            foreach($pieces as $piece) {
                //drill down to what we're looking for
                $postedParams = $postedParams[$piece];
            }

        }
        if(!array_key_exists($key, $postedParams)) {
            $postedParams[$key] = '';
        }
        //now iterate each validator and kick out if we fail
        foreach($fieldConfig as $item) {

            $validatorName = $item['class'];
            $params = array();

            if(array_key_exists('params', $item)) {
                $params = $item['params'];
            }

            $validator = $this->getFactory()->getValidator($validatorName, $params);

            if(!$validator->validate($postedParams[$key])) {

                return $item['failkey'];
            }
        }

        return true;
    }

}