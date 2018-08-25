<?php
/**
 * Created by PhpStorm.
 * User: davemeikle
 * Date: 2018-08-23
 * Time: 10:33 PM
 */

namespace Gossamer\Ra\Security\Tests\Managers;


use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Ra\Security\Authorization\Voters\VoterInterface;
use Gossamer\Ra\Security\Managers\AccessControlManager;
use Gossamer\Ra\Security\Tests\Traits\TokenTraitsForTesting;
use tests\BaseTest;
class AccessControlManagerTest extends BaseTest
{


    use LoadConfigurationTrait;
    use TokenTraitsForTesting;
    
    public function testInstantiateManager() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        $token = $this->getToken($ymlKey, 'members::100', ['IS_MEMBER', "IS_MANAGER"]);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);
        $this->assertEquals($manager->execute($voterConfig[$ymlKey]), VoterInterface::ACCESS_GRANTED);
    }

    /**
     * @throws \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     * @throws \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     *
     * @expectedException \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     */
    public function testUnauthorizedAccess() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        //member is not a manager, trying to access a different member
        $token = $this->getToken($ymlKey, 'members::100', ['IS_MEMBER']);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);

        $manager->execute($voterConfig[$ymlKey]);
    }

    /**
     * @throws \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     * @throws \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     *
     * @expectedException \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     */
    public function testAccessControlKeyNotSetAccess() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        //member is not a manager, trying to access a different member
        $token = $this->getToken($ymlKey, 'members::101', ['IS_MEMBER']);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);

        //remove the key to cause an error to be thrown
        unset($voterConfig[$ymlKey]['access_control']);
       
        $manager->execute($voterConfig[$ymlKey]);
    }

    /**
     * @throws \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     * @throws \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     *
     * @expectedException \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     */
    public function testSubjectKeyNotSetAccess() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        //member is not a manager, trying to access a different member
        $token = $this->getToken($ymlKey, 'members::101', ['IS_MEMBER']);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);

        //remove the key to cause an error to be thrown
        unset($voterConfig[$ymlKey]['access_control']['subject']);

        $manager->execute($voterConfig[$ymlKey]);
    }

    /**
     * @throws \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     * @throws \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     *
     * @expectedException \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     */
    public function testRolesKeyNotSetAccess() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        //member is not a manager, trying to access a different member
        $token = $this->getToken($ymlKey, 'members::101', ['IS_MEMBER']);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);

        //remove the key to cause an error to be thrown
        unset($voterConfig[$ymlKey]['access_control']['roles']);

        $manager->execute($voterConfig[$ymlKey]);
    }

    /**
     * @throws \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     * @throws \Gossamer\Ra\Exceptions\UnauthorizedAccessException
     *
     * @expectedException \Gossamer\Core\Configuration\Exceptions\KeyNotSetException
     */
    public function testRulesKeyNotSetAccess() {
        $ymlKey = 'test_manager';
        $httpRequest = $this->getHttpRequest(
            '/member/members::100',
            $this->getRequestParams(array('/member/members::100'=> '','id'=>123), array(),array('members::100'), 'GET'),
            'GET'
        );
        $configPath = __SITE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Gossamer' . DIRECTORY_SEPARATOR . 'Ra' . DIRECTORY_SEPARATOR
            . 'Security' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'voterconfig.yml';
        $voterConfig = $this->loadConfig($configPath);

        //member is not a manager, trying to access a different member
        $token = $this->getToken($ymlKey, 'members::101', ['IS_MEMBER']);
        setSession('_security_secured_area', serialize($token));
        $manager = new AccessControlManager($this->getLogger(), $httpRequest);

        //remove the key to cause an error to be thrown
        unset($voterConfig[$ymlKey]['access_control']['rules']);

        $manager->execute($voterConfig[$ymlKey]);
    }


}