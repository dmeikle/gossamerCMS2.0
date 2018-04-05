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
 * Time: 8:15 PM
 */

namespace Gossamer\Core\Components\Security\Traits;


use Gossamer\Core\Components\Security\Core\Client;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Ra\Security\SecurityToken;

//use extensions\textlogin\security\Client;

trait GetLoggedInMemberTrait {

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    /**
     *
     * @return int
     */ 
    protected function getLoggedInMember() {
        $token = $this->getSecurityToken();

        if (!is_object($token) || is_null($token->getClient())) {
            if($this->isDebugMode()) {
                error_log('warning: DEBUG MODE IS SET TO TRUE IN CONFIG.YML');
                return new Client(array(
                    'ipAddress' => 'localhost',
                    'id' => 'debug::id',
                    'email' => 'debugmodeistrue@test.com',
                    'memberID' => 'BB0044',
                    'memberPrefix' => 'CV'
                ));

            }

            return null;
        }

        return $token->getClient();
    }

    /**
     *
     * @return SecurityToken
     */
    protected function getSecurityToken() {
        $serializedToken = getSession('_security_secured_area');
        $token = unserialize($serializedToken);

        return $token;
    }
 

    private function isDebugMode() {
       $config = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'config.yml');
        
        return (array_key_exists('DEBUG_MODE', $config) && $config['DEBUG_MODE'] == 'true');
    }

    public function setHttpRequest(HttpRequest $httpRequest) {
        $this->httpRequest = $httpRequest;
    }
}

