<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2/28/2017
 * Time: 11:05 PM
 */

namespace Gossamer\Horus\Traits;


trait SessionTrait
{


    protected function getSession($key) {
        $session = $_SESSION;

        return $this->fixObject($session[$key]);
    }

    protected function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }

    protected function fixObject(&$object) {
        if (!is_object($object) && gettype($object) == 'object') {

            return ($object = unserialize(serialize($object)));
        }

        return $object;
    }
}