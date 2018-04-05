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
 * Date: 4/6/2017
 * Time: 10:47 PM
 */

namespace Gossamer\Core\System;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Set\Utils\Container;

interface KernelRunnerInterface
{
    public function __construct(HttpRequest &$httpRequest, HttpResponse &$httpResponse, Container $container);

    public function execute(array $nodeConfig);
}