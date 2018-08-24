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
 * Date: 8/23/2018
 * Time: 12:41 PM
 */

namespace Gossamer\Ra\Security\Authorization\Voters;


use Gossamer\Ra\Security\Roles\Role;
use Gossamer\Ra\Security\TokenInterface;
use Gossamer\Ra\Security\Authorization\Voters\AbstractVoter;
use Gossamer\Ra\Security\Authorization\Voters\VoterInterface;

class CheckUserByRolesVoter extends AbstractVoter implements VoterInterface
{


    /**
     * @param TokenInterface $token
     * @param $subject
     * @param array $attributes
     * @return int ACCESS_GRANTED|ACCESS_ABSTAIN|ACCESS_DENIED
     *
     */
    public function vote(TokenInterface $token, $subject, array $attributes) {
        $retval = VoterInterface::ACCESS_ABSTAIN;
        $roles = $this->getRoles($token);

        //start by getting the roles passed in
        foreach ($attributes as $attribute) {
            if($attribute instanceof Role) {
                $attribute = $attribute->getRole();
            }

            $retval = VoterInterface::ACCESS_DENIED;

            //this is a string array
            foreach($roles as $role) {
                if($attribute === $role && $this->checkRules($token, $subject, $role)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return $retval;
    }

    /**
     * @param TokenInterface $token
     *
     * @return string[]
     */
    protected function getRoles(TokenInterface $token) {
        return $token->getRoles();
    }

    /**
     * @param TokenInterface $token
     *
     * @return string
     */
    protected function getUserid(TokenInterface $token) {
        return $token->getClient()->getId();
    }

    protected function checkRules(TokenInterface $token, $subject, string $role) {
        //check to see if it's a manager checking on a member of a group
        if(isset($this->voterConfig['ignoreRolesIfNotSelf'])) {
            //it's not self so lets see if we have permission to access
            if($this->getUserid($token) != $subject['id']) {
                return (!in_array($role, $this->voterConfig['ignoreRolesIfNotSelf']));
            }
        }
        //let's see if we're just looking at our own and that we also allowed to
        if(isset($this->voterConfig['self']) && $this->voterConfig['self'] == 'true') {
            return true;
        }

        return false;
    }


}