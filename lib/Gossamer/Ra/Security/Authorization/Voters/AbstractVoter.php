<?php
/**
 * Created by PhpStorm.
 * User: davemeikle
 * Date: 2018-08-23
 * Time: 3:19 PM
 */

namespace Gossamer\Ra\Security\Authorization\Voters;


use Gossamer\Horus\Http\HttpRequest;

class AbstractVoter
{

    protected $voterConfig;

    protected $httpRequest;

    /**
     * AbstractVoter constructor.
     * @param array $voterConfig
     *
     * voterConfig is the rules passed in under access management.
     * example:
     * members_get:
        access_control:
            roles:
                - IS_MEMBER
                - IS_MEMBER_MANAGER
            rules:
                - { class: 'Gossamer\Ra\Authorization\Voters\CheckUserByRolesVoter', self: true, ignoreRolesIfNotSelf: [IS_MEMBER] }
                - { class: 'Gossamer\Ra\Authorization\Voters\CheckUserByGroupVoter', self: true, ignoreRolesIfNotSelf: [IS_MEMBER] }
     */
    public function __construct(array $voterConfig, HttpRequest $httpRequest)
    {
        $this->voterConfig = $voterConfig;
        $this->httpRequest = $httpRequest;
    }
}