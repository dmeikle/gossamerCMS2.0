<?php
/**
 * Created by PhpStorm.
 * User: davemeikle
 * Date: 2018-08-23
 * Time: 10:33 PM
 */

namespace Gossamer\Ra\Security\Tests\Managers;


use Gossamer\Ra\Security\Managers\AccessControlManager;
use tests\BaseTest;

class AccessControlManagerTest extends BaseTest
{

    public function testInstantiateManager() {
        $httpRequest = $this->getHttpRequest('/member/login/success', self::$requestParams, 'GET');

        $manager = new AccessControlManager($this->getLogger(), $httpRequest);
        $manager->execute($this->getConfig());
    }

    private function getConfig() {
        return array(

                    "subject" => array( "param" => 'id', 'method' => 'uri'),
                  "roles" => array("IS_MEMBER","IS_MEMBER_MANAGER"),
                  "rules" => array(
                      array(
                        "class"=> "Gossamer\\Ra\\Security\\Authorization\\Voters\\CheckUserByRolesVoter",
                      "self"=> true,
                      "ignoreRolesIfNotSelf"=> array("IS_MEMBER")
                      ),
                    array(
                        "class"=> "Gossamer\\Ra\\Security\\Authorization\\Voters\\CheckUserByGroupVoter",
                      "self"=> true,
                      "ignoreRolesIfNotSelf"=> array("IS_MEMBER")
                    )
                  )

            );
    }
}