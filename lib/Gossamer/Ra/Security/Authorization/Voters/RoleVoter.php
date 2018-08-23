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

namespace Gossamer\Ra\Authorization\Voters;


use Gossamer\Ra\Security\Roles\Role;
use Gossamer\Ra\Security\TokenInterface;

class RoleVoter implements VoterInterface
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

        foreach ($attributes as $attribute) {
            if($attribute instanceof Role) {
                $attribute = $attribute->getRole();
            }

            $retval = VoterInterface::ACCESS_DENIED;

            //this is a string array
            foreach($roles as $role) {
                if($attribute === $role) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return $retval;
    }

    /**
     * @param TokenInterface $token
     * @return string[]
     */
    protected function getRoles(TokenInterface $token) {
        return $token->getRoles();
    }
}