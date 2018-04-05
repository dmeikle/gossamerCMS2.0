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
 * Date: 3/1/2017
 * Time: 10:36 PM
 */

namespace Gossamer\Nephthys\Rest;


use Psr\Http\Message\RequestInterface;

interface RestInterface
{

    public function query($requestMethod, $model, $verb, array $params);

    public function sendAsync(RequestInterface $request, array $options = []);

    public function send(RequestInterface $request, array $options = []);

    public function requestAsync($method, $uri = '', array $options = []);

    public function request($method, $uri = '', array $options = []);

    public function getConfig($option = null);

}