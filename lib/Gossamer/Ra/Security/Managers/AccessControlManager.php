<?php
/**
 * Created by PhpStorm.
 * User: davemeikle
 * Date: 2018-08-23
 * Time: 4:22 PM
 */

namespace Gossamer\Ra\Security\Managers;


use Gossamer\Core\Configuration\Exceptions\KeyNotSetException;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Ra\Exceptions\UnauthorizedAccessException;
use Gossamer\Ra\Security\Authorization\Voters\VoterInterface;
use Gossamer\Ra\Security\Roles\Role;
use Gossamer\Set\Utils\ContainerTrait;
use Gossamer\Ra\Security\Authorization\Voters\Voter;

class AccessControlManager
{

    use ContainerTrait;

    protected $logger;


    protected $container;

    protected $request;

    protected $response;

    public function __construct(LoggingInterface $logger, HttpRequest &$request, HttpResponse $response = null) {
        $this->logger = $logger;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param array $securityConfig
     * @return bool
     * @throws KeyNotSetException
     * @throws UnauthorizedAccessException
     */
    public function execute(array $securityConfig) {

        if(!array_key_exists('access_control', $securityConfig)) {
            throw new KeyNotSetException('access_control not set in security config');
        }
        $securityConfig = $securityConfig['access_control'];

        if(!array_key_exists('roles', $securityConfig)) {
            throw new KeyNotSetException('roles not set in security config');
        }
        if(!array_key_exists('subject', $securityConfig)) {
            throw new KeyNotSetException('subject not set in security config');
        }
        if(!array_key_exists('rules', $securityConfig)) {
            throw new KeyNotSetException('rules not set in security config');
        }

        $attributes = $this->buildRoles($securityConfig['roles']);
        $token = @unserialize(getSession('_security_secured_area'));

        foreach($securityConfig['rules'] as $rule ) {
            $voterName = $rule['class'];
            $voter = new $voterName($rule, $this->request);

            if($voter->vote($token, $this->getSubject($securityConfig['subject']), $attributes) == VoterInterface::ACCESS_DENIED) {
               throw new UnauthorizedAccessException();
            }
        }

        return true;
    }


    /**
     * @param array $roles
     * @return array
     */
    private function buildRoles(array $roles) {
        $retval = array();

        foreach($roles as $roleName) {
            $retval[] = new Role($roleName);
        }

        return $retval;
    }


    /**
     * @param $subject
     * @return mixed|null
     */
    private function getSubject($subject) {
        if($subject['method'] == 'query') {
            return $this->request->getRequestParams()->getQuerystringParameter($subject['param']);
        }
        if($subject['method'] == 'uri') {
            return $this->request->getRequestParams()->getUriParameter($subject['param']);
        }
        if($subject['method'] == 'post') {
            return $this->request->getRequestParams()->getPostParameter($subject['param']);
        }

        return null;
    }

}