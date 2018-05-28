<?php

namespace Gossamer\Aset\Validation\Validators;


use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * CurrencyValidator - receives an IP and validates only if it holds a value
 * 
 * @author	Dave Meikle
 * 
 * @copyright 2007 - 2014
 */
class TelephoneValidator extends AbstractValidator implements FlyweightValidatorInterface{
    
    /** Creates a new instance of EmailValidatorCommand */
    public function __construct() {
        //there are a variety of formats - for now, stick to a strict format:
    	// 1-123-123-1234 x 234
    	// 1-123-123-1234
    	// 1-123-123-1234 ext 234
    	// 1-123-123-1234 x234
    	// 123-123-1234
    	// we can revisit this if we need to change for different country
    
    	parent::__construct("/^([0-9])?(\-|\s|\+)?([0-9]{3})+(\-|\s|\+)?([0-9]{3})(\-|\s|\+)?([0-9]{4})(\s)?((ext|x)?((\s)?[0-9])+)?$/");
    }


    /**
     * method validate
     * 
     * @param string 		action
     * @param ValidationItem 	object
     * 
     * @return boolean
     */
    public function validate($value) {
        return $this->checkValidChars($value);
    }
}

