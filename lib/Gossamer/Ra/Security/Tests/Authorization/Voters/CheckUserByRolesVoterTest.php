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
 * Time: 10:38 AM
 */

namespace Gossamer\Ra\Security\Tests\Authorization\Voters;


use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Ra\Security\Authorization\Voters\CheckUserByRolesVoter;
use Gossamer\Ra\Security\Authorization\Voters\VoterInterface;
use Gossamer\Ra\Security\Roles\Role;
use Gossamer\Ra\Security\Tests\Traits\TokenTraitsForTesting;
use tests\BaseTest;

class CheckUserByRolesVoterTest extends BaseTest
{

    use LoadConfigurationTrait;
    use TokenTraitsForTesting;

    /**
     * check to ensure a member can pass the security of being able to check themself.
     */
    public function testIsMemberVoterCheckingSelf() {
        $ymlKey = 'test_check_self_voter';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100' => '', 'id' => 123), array(), array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        $voter = new CheckUserByRolesVoter($voterConfig[$ymlKey]['access_control']['rules'][0], $httpRequest);
        $token = $this->getToken($ymlKey, 'members::100', ['IS_MEMBER', "IS_MANAGER"]);
        $subject = array('id' => 'members::100');
        $roles = array('IS_MEMBER');
        $this->assertEquals($voter->vote($token, $subject, $this->buildRoles($roles)), VoterInterface::ACCESS_GRANTED);
    }


    /**
     * check to ensure a member cannot check another member profile unless they have proper permissions
     */
    public function testIsMemberVoterCheckingOtherMember() {
        $ymlKey = 'test_check_self_voter';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100' => '', 'id' => 123), array(), array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        $voter = new CheckUserByRolesVoter($voterConfig[$ymlKey]['access_control']['rules'][0], $httpRequest);
        $token = $this->getToken($ymlKey, 'members::100', ['IS_MEMBER', "IS_MANAGER"]);
        //set this member to a different ID
        $subject = array('id' => 'members::101');
        $roles = array('IS_MEMBER');
        $this->assertEquals($voter->vote($token, $subject, $this->buildRoles($roles)), VoterInterface::ACCESS_DENIED);
    }

    /**
     * check to ensure a manager can check another member profile if they have proper permissions
     */
    public function testIsManagerVoterCheckingOtherMember() {
        $ymlKey = 'test_check_self_voter';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100' => '', 'id' => 123), array(), array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        $voter = new CheckUserByRolesVoter($voterConfig[$ymlKey]['access_control']['rules'][0], $httpRequest);
        $token = $this->getToken($ymlKey, 'members::100', ['IS_MEMBER', "IS_MANAGER"]);
        //set this member to a different ID
        $subject = array('id' => 'members::101');
        $roles = array('IS_MEMBER', 'IS_MANAGER');
        $this->assertEquals($voter->vote($token, $subject, $this->buildRoles($roles)), VoterInterface::ACCESS_GRANTED);
    }



    private function buildRoles(array $roles) {
        $retval = array();
        foreach ($roles as $role) {
            $retval[] = new Role($role);
        }

        return $retval;
    }
}