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
 * Date: 3/19/2017
 * Time: 11:30 AM
 */

namespace Gossamer\Core\Http\Managers;

use Gossamer\Core\Http\HttpRequest;


/**
 * Used for encrypting and decrypting cookies we want to access
 *
 * @author Dave Meikle
 */
class CookieManager {

    private $config = null;

    private $httpRequest;

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    public function __construct(HttpRequest &$request) {
        $this->httpRequest = $request;
        $this->getCookieCredentials();
    }

    /**
     * accessor
     *
     * @param string $cookieName
     * @param array $values
     */
    public function setCookie($cookieName, array $values) {

        $cookieValue = json_encode($values);
        $name = $this->config['name'] . "[$cookieName]";

        if ($this->config['secure'] == 'true') {
            $cookieValue = $this->encrypt($cookieValue, $cookieName);
        }

        setcookie($name, $cookieValue, time() + 86400, "/"); //86400 = 1 day
    }

    /**
     * accessor
     *
     * @param string $cookieName
     *
     * @return string
     */
    public function getCookie($cookieName) {

        if (!array_key_exists($this->config['name'], $_COOKIE)) {
            return null;
        }

        $cookie = $_COOKIE[$this->config['name']];

        if (!array_key_exists($cookieName, $cookie)) {
            return null;
        }

        $cookieValue = $cookie[$cookieName];

        if ($this->config['secure'] == 'true') {

            $cookieValue = $this->decrypt($cookieValue, $cookieName);
        }
        //strip off any garbage text from decoding
        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $cookieValue), true);
    }

    /**
     * encrypt the cookie
     *
     * @param string $data
     * @param string $key
     *
     * @return string
     */
    private function encrypt($data, $key) {

        $key = substr(hash('sha256', $this->config['salt'] . $key . $this->config['salt']), 0, 32);
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
     *
     * @return string
     */
    private function decrypt($data, $key) {

        $key = substr(hash('sha256', $this->config['salt'] . $key . $this->config['salt']), 0, 32);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);

        return $decrypted;
    }

    /**
     * loads configuration for cookies from the config file.
     * relies on included trait LoadConfig
     *
     * cookies:
        secure: true
        salt: th!zi&YorL0n&saltv!s?
        name: nameofcookiehere
     */
    private function getCookieCredentials() {

        //load from trait
        $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'config.yml');

        $this->config = $config['cookies'];
    }

}