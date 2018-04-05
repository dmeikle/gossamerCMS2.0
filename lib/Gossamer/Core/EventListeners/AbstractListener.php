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
 * Date: 3/20/2017
 * Time: 12:36 AM
 */

namespace Gossamer\Core\EventListeners;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Monolog\Logger;

abstract class AbstractListener extends \Gossamer\Horus\EventListeners\AbstractListener
{

    protected $httpRequest;

    protected $httpResponse;

    public function __construct(Logger $logger, HttpRequest $request, HttpResponse $response) {
        $this->logger = $logger;
        $this->httpRequest = $request;
        $this->httpResponse = $response;
    }
}