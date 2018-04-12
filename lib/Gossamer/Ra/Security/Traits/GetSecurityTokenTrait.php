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
 * Date: 10/31/2017
 * Time: 10:22 PM
 */

namespace Gossamer\Ra\Security\Traits;


use Gossamer\Horus\Http\HttpRequest; 
use Gossamer\Ra\Security\Client;
use Gossamer\Ra\Security\SecurityToken;

trait GetSecurityTokenTrait
{

    public function getSecurityToken() {
        $serializedToken = getSession('_security_secured_area');
        $token = unserialize($serializedToken);

        return $token;
    }

    /**
     * @param Client $client
     * @param HttpRequest &$request
     *
     * intended for use when a user logs in with SSO but has no account with us yet.
     * a token is issued for the SSO login but no account with us exists until a member
     * account is created. We then need to update the logged in user token to add the
     * new member ID information to the client token
     */
    public function updateTokenClient(Client $client, HttpRequest &$request) {
        $token = $this->getSecurityToken();

        $newToken = new SecurityToken($client, $request->getYmlKey(), $token->getRoles());
        setSession('_security_secured_area', serialize($newToken));
    }
}