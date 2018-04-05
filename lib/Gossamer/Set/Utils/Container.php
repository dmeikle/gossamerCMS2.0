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
 * Date: 3/1/2017
 * Time: 10:36 PM
 */

namespace Gossamer\Set\Utils;

use Gossamer\Set\Utils\Exceptions\ObjectNotFoundException;

class Container 
{

    private $directory = array();

    /**
     * remove all items from memory
     */
    public function __destruct() {
        while (count($this->directory) > 0) {
            try {
                $item = array_pop($this->directory);
                unset($item);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws ObjectNotFoundException
     */
    public function get($key) {

        if (!array_key_exists($key, $this->directory)) {
//            
//            echo debug_backtrace()[0]['function']."<br>\r\n";
//            echo debug_backtrace()[1]['function']."<br>\r\n";
//            echo debug_backtrace()[2]['function']."<br>\r\n";
//            echo debug_backtrace()[3]['function']."<br>\r\n";
//            echo debug_backtrace()[4]['function']."<br>\r\n";
            throw new ObjectNotFoundException($key . ' does not exist in container');
        }

        return $this->directory[$key];
    }


    /**
     * @param $key
     * @param $object
     */
    public function set($key, &$object) {
        $this->directory[$key] = $object;
    }
}