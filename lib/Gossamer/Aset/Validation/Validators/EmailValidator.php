<?php

namespace Gossamer\Aset\Validation\Validators;


use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * EmailValidator - receives an email and validates only if it holds a value
 * 
 * @author	Dave Meikle
 * 
 * @copyright 2007 - 2014
 */
class EmailValidator extends AbstractValidator implements FlyweightValidatorInterface{
    
    /** Creates a new instance of EmailValidatorCommand */
    public function __construct() {
        parent::__construct("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/");
    }


    /**
     * validate
     * 
     * @param string 	action
     * @param ValidationItem 	object
     * 
     * @return boolean
     */
     public function validate($value) {
        //the object contains a pass/fail flag within it...
        return $this->checkValidChars($value);
    }

}

