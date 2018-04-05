<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Core\Navigation;

use Gossamer\Core\Components\Security\Core\Client;
use Gossamer\Essentials\Configuration\Traits\LoadConfigurationTrait;

/**
 * MenuManager
 *
 * @author Dave Meikle
 */
class MenuManager {

    use LoadConfigurationTrait;

    /**
     * checks to see if the logged in user has the right to view a menu
     * we want to dynamically draw to the page.
     *
     * THIS WORKS ON CALLS FROM THE TOP LEVEL ONLY. CALLS ATTEMPTED WITHIN
     * A CALL HAVE NO ACCESS TO THE CLIENT SESSION INFORMATION AND WILL ASSUME
     * NO ONE IS LOGGED IN.
     *
     * @param string $ymlkey
     * @param Client $client
     */
    public function checkAccessRights($ymlkey, Client $client = null) {
        $roles = $this->getUriRoles($ymlkey);

        if ($roles == false) {
            //it has no roles defined so it's open to all
            return true;
        }
        $clientRoles = array();
        if (!is_null($client)) {
            $clientRoles = $client->getRoles();
        }

        $accessRoles = array_intersect($roles, $clientRoles);

        return (is_array($accessRoles) && count($accessRoles) > 0);
    }

    private function getUriRoles($ymlkey) {
        $config = $this->loadComponentConfig($ymlkey, 'security');

        if (!is_null($config) && array_key_exists('roles', $config)) {
            return $config['roles'];
        }

        return false;
    }

}
