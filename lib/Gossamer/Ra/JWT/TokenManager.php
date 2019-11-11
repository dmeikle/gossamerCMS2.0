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
 * Time: 8:18 PM
 */

namespace Gossamer\Ra\JWT;


use Gossamer\Essentials\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Essentials\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Ra\Exceptions\TokenExpiredException;
use Gossamer\Ra\Security\Traits\EncryptionTrait;

class TokenManager
{
    const JWT_LIFE_SPAN = "+60 minutes";
    const JWT_EXPIRATION_TIME = 'JWT_EXPIRATION_TIME';
    const JWT_TOKEN = 'TOKEN';
    const KEY = 'key';
    use LoadConfigurationTrait;
    use EncryptionTrait;

    private $list = array();

    private $httpRequest;

    public function __construct(HttpRequest $httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    public function add($key, $values) {
        $this->list[$key] = $values;
    }

    public function setSession(array $session) {
        $this->list = $session;
    }

    public function getEncryptedJwtToken() {
        //first load the salt
        $salt = $this->loadSalt();

        $expirationTime = strtotime(self::JWT_LIFE_SPAN, strtotime(self::JWT_LIFE_SPAN, microtime(true)));
        //since objects in session get lost we have to serialize everything in a wrapper before encrypting it
        //return $this->encrypt(serialize(array(self::JWT_EXPIRATION_TIME => $expirationTime, self::JWT_TOKEN => ($this->list))), self::KEY, $salt);
        return $this->encrypt(json_encode(array(self::JWT_EXPIRATION_TIME => $expirationTime, self::JWT_TOKEN => ($this->list))), self::KEY, $salt);
    }

    public function getDecryptedJwtToken($jwtString) {

        //first load the salt
        $salt = $this->loadSalt();

        $jwtToken = $this->decrypt($jwtString, self::KEY, $salt);

        //since the token was serialized before encrypting it (to preserve objects) we unserialize it now
       // $jwtToken = unserialize(trim($jwtToken));
        $jwtToken = json_decode(trim($jwtToken), true);

        $expirationTime = $jwtToken[self::JWT_EXPIRATION_TIME];

        if($expirationTime < microtime(true)) {
         //   throw new TokenExpiredException();
        }

        $token = ($jwtToken[self::JWT_TOKEN]);

        return $token;
    }


    private function loadSalt() {
        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . DIRECTORY_SEPARATOR . 'config.yml');
        if (!array_key_exists('system', $config) || !array_key_exists('salt', $config['system'])) {
            throw new KeyNotSetException('system salt not defined in site config');
        }

        return $config['system']['salt'];
    }

    public function getEncryptedString(array $values) {
        //first load the salt
        $salt = $this->loadSalt();

        return $this->encrypt(json_encode($values), self::KEY, $salt);
    }

    public function getDecryptedString($values) {
        //first load the salt
        $salt = $this->loadSalt();
        $token = $this->decrypt($values, self::KEY, $salt);

        return json_decode(trim($token), true);
    }
}