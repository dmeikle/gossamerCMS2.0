<?php

namespace Gossamer\Aset\Validation\Factory;

use Gossamer\Aset\Validation\Exceptions\InterfaceNotImplementedException;
use Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface;

/**
 * Description of ValidatorCommandFactory
 *
 * @author davem
 */
class ValidatorCommandFactory {
    
    private $validators = array();
    
    public function getValidator($validatorName, array $params) {
        //these ones are custom on each call
        if(array_key_exists('mask', $params)) {
            $validatorName = $this->getValidatorPath($validatorName);
            $validator = new $validatorName($params['mask']);
            if(!$validator instanceof \Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface) {
                throw new InterfaceNotImplementedException($validatorName . ' does not implement FlyweightValidatorInterface');
            }

            return $validator;
        }

        if(!array_key_exists($validatorName, $this->validators)) {
            $validator = $this->getValidatorPath($validatorName);

            $this->validators[$validatorName] = new $validator();

            if(!$this->validators[$validatorName] instanceof \Gossamer\Aset\Validation\Factory\FlyweightValidatorInterface) {
                throw new InterfaceNotImplementedException($validatorName . ' does not implement FlyweightValidatorInterface');
            }
        }


        return $this->validators[$validatorName]->setParams($params);
    }
    
    private function getValidatorPath($validatorName) {
        if(strpos($validatorName, '\\') === false) {
            return 'Gossamer\\Aset\\Validation\\Validators\\' . $validatorName . 'Validator';
        }
        //developer is overwriting with an external validator
        return  $validatorName;
    }
}
