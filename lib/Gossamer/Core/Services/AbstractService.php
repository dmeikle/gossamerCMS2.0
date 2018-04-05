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
 * Date: 3/9/2017
 * Time: 9:47 PM
 */

namespace Gossamer\Core\Services;


use Gossamer\Essentials\Configuration\ConfigLoader;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;

abstract class AbstractService
{
    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    protected $logger;

    protected $loader;

    protected $httpRequest;

    protected $httpResponse;


    public function __construct(LoggingInterface $logger, ConfigLoader $loader = null) {
        $this->logger = $logger;
        $this->loader = $loader;
    }

    public function setHttpRequest(HttpRequest &$httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    public function setHttpResponse(HttpResponse &$httpResponse) {
        $this->httpResponse = $httpResponse;
    }
}