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
 * Date: 3/7/2017
 * Time: 8:41 PM
 */

namespace Gossamer\Nephthys\Rest;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class GuzzleClient extends Client implements RestInterface
{

    public function query($requestMethod, $model, $verb, array $params) {
        // TODO: Implement query() method.
    }
}