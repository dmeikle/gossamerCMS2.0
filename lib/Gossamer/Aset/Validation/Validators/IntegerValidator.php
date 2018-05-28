<?php

namespace Gossamer\Aset\Validation\Validators;


use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * IntegerValidator - receives an address and validates only if it holds a value
 * 
 * @author	Dave Meikle
 * 
 * @copyright 2007 - 2014
 */
class IntegerValidator extends AbstractValidator implements FlyweightValidatorInterface{
    
    /** Creates a new instance of EmailValidatorCommand */
    public function __construct() {
        parent::__construct("^[0-9]$^");
    }


    /**
     * validate
     * 
     * @param string 	action
     * @param string 	value
     * 
     * @return boolean
     */
     public function validate($value) {
        //the object contains a pass/fail flag within it...
        return $this->checkValidChars($value);
    }

}

