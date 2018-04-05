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
 * Date: 3/7/2017
 * Time: 12:12 AM
 */

namespace Gossamer\Ra\Encryption;


/**
 * Encrypts any value passed in
 *
 * @author Dave Meikle
 */
class Encryption {

    /**
     * generates the hash
     *
     * @param string $password
     */
    public static function generateHash($password) {
        // A higher "cost" is more secure but consumes more processing power
        $cost = 10;

        // Create a random salt
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');

        // Prefix information about the hash so PHP knows how to verify it later.
        // "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        // Value:
        // $2a$10$eImiTXuWVxfM37uY4JANjQ==
        // Hash the password with the salt
        $hash = crypt($password, $salt);

        return $hash;
    }

    /**
     *
     * @param string $password
     * @param string $hash
     *
     * @return boolean
     */
    public static function compareHash($password, $hash) {
        return crypt($password, $hash) === $hash;
    }

}
