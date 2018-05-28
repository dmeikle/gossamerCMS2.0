<?php

namespace Gossamer\Aset\Validation\Validators;

use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * Description of RequiredCommand
 *
 * @author davem
 */
class RequiredValidator extends AbstractValidator implements FlyweightValidatorInterface {
    
    const MIN_LENGTH = 'minlength';
    
    const MAX_LENGTH = 'maxlength';
    
    
    /** Creates a new instance of RequiredValidator */
    public function __construct() {
        
    }
    
    

    public function validate($value) {
        if(array_key_exists(self::MIN_LENGTH, $this->params)) {
            if(strlen($value) < intval($this->params[self::MIN_LENGTH])) {
                return false;
            }
        }
        if(array_key_exists(self::MAX_LENGTH, $this->params)) {
            if(strlen($value) > intval($this->params[self::MAX_LENGTH])) {
                return false;
            }
        }
        
        return strlen($value) > 0;
    }
}
