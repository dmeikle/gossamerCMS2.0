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
 * Date: 9/17/2017
 * Time: 11:19 AM
 */

namespace Gossamer\Core\Serialization;


class Serializer
{

    protected $fields = array();

    /**
     * @param array $entity
     * @param array $fields optional list of fields for recursive padding
     *
     * goes through an array to find keys that are not existing.
     * if not found, it will add the key to avoid 'index not found'
     * later when we try to use it, perhaps when rendering a page
     */
    public function padEntityValues(array &$entity, array $fields = null) {

        if(is_null($fields)) {
            $fields = $this->fields;
        }

        foreach($fields as $key => $value) {
            if(!array_key_exists($key,$entity)) {
                $entity[$key] = $value;
            }elseif(is_array($value)) {
                $this->padEntityValues($entity[$key], $value);
            }
        }

    }
}