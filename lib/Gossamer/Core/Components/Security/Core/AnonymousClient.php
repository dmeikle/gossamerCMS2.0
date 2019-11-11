<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2019-11-09
 * Time: 8:49 PM
 */

namespace lib\Gossamer\Core\Components\Security\Core;


use Gossamer\Core\Components\Security\Core\Client;
use Gossamer\Horus\Http\Traits\ClientIPAddressTrait;

class AnonymousClient extends Client
{

    use ClientIPAddressTrait;

    public function __construct(array $params = array())
    {
        parent::__construct(array(
            'roles' => 'IS_ANONYMOUS',
            'ipAddress' => $this->getIpAddress()
        ));
    }
}