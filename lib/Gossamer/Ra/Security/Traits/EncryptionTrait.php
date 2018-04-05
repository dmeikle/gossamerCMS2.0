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
 * Date: 2/8/2018
 * Time: 8:22 PM
 */

namespace Gossamer\Ra\Security\Traits;


trait EncryptionTrait
{


    
    
    /**
     * encrypt the cookie
     * 
     * @param $data
     * @param $key
     * @param $salt
     * 
     * @return mixed
     */
    private function encrypt($data, $key, $salt) {

        $key = substr(hash('sha256', $salt . $key . $salt), 0, 32);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));

        return $encrypted;
    }

    /**
     * decrypt the cookie
     *
     * @param string $data
     * @param string $key
     * @param $salt
     *
     * @return string
     */
    private function decrypt($data, $key, $salt) {

        $key = substr(hash('sha256', $salt . $key . $salt), 0, 32);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);

        return $decrypted;
    }
}