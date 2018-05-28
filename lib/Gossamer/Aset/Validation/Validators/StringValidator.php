<?php

namespace Gossamer\Aset\Validation\Validators;

use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * Description of RequiredCommand
 *
 * @author davem
 */
class StringValidator extends AbstractValidator implements FlyweightValidatorInterface {
   
    
    /** Creates a new instance of StringValidatorCommand */
    public function __construct() {
        parent::__construct("/^[a-zA-Z\\s-\']+$/");
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


