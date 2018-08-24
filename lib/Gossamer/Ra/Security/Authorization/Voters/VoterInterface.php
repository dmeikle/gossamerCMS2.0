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
 * Time: 12:29 PM
 */

namespace Gossamer\Ra\Security\Authorization\Voters;


use Gossamer\Ra\Security\TokenInterface;

interface VoterInterface
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    /**
     * @param TokenInterface $token
     * @param $subject
     * @param array $attributes
     * @return int ACCESS_GRANTED|ACCESS_ABSTAIN|ACCESS_DENIED
     *
     */
    public function vote(TokenInterface $token, $subject, array $attributes);
}