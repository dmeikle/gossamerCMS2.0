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
 * Date: 8/25/2018
 * Time: 10:50 AM
 */

namespace Gossamer\Ra\Security\Tests\Traits;


use Gossamer\Ra\Security\Client;
use Gossamer\Ra\Security\SecurityToken;

trait TokenTraitsForTesting
{

    protected function getToken($ymlKey, $memberId, array $roles) {
        $client = new Client();
        $client->setId($memberId);
        $client->setIpAddress('1.1.1.1');
        $client->setRoles($roles);

        return $this->generateNewToken($client, $ymlKey);
    }

    protected function generateNewToken(Client $client, $ymlKey) {

        $token = new SecurityToken($client, $ymlKey, $client->getRoles());

        return $token;
    }
}