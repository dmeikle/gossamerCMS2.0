<?php

namespace Gossamer\Aset\Validation\Validators;

/**
 * Description of AbstractValidator
 *
 * @author davem
 */
abstract class AbstractValidator {
   
    protected $params;
    
    protected $regex;
    
    public function __construct($regex) {
        $this->regex = $regex;
    }
    
    /**
     * 
     * @param array $params
     * 
     * @return this
     */
    public function setParams(array $params) {
        $this->params = $params;
        
        return $this;
    }
    
    /**
     * method checkValidChars - does the actual checking
     * 
     * @param ValidationItem 	object
	 *
     */
    protected function checkValidChars($value){
    	
    	if(!is_array($value) && strlen($value) == 0 || is_array($value) && count($value) == 0) {
            return true;
    	}
		
        if(is_array($value)) {
            foreach($value as $row) {
                if(!preg_match($this->regex,$row)) {
                    //fail right away
                    return false;
                }
            }
        } else {
            if(preg_match($this->regex,$value)) {
                return true;
            }
        }			
    }
    
    /**
     * method checkValidCharsAgainstString - does the actual checking
     * 
     * @param ValidationItem 	object
	 * @param string			valid character list
	 *
     */
    protected function checkValidCharsAgainstString($value, $expression){
        
        //loop through the character array checking each character exists in the expression to validate against
        for($i = 0; $i < count($chars); $i++) {
        	
            $char = $value[i];
			
            if(strpos($expression,$char) < 0) {
            	
            	return false;
            }
                       
        }
		
        return true;
    }
}
