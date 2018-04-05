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
 * Date: 3/5/2017
 * Time: 10:27 PM
 */

namespace Gossamer\Core\MVC;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;

abstract class AbstractView
{

    protected $httpRequest;

    protected $httpResponse;

    protected $ymlKey;

    protected $agentType;

    protected $logger;

    protected $headers;
    

    use \Gossamer\Set\Utils\ContainerTrait;

    public function __construct(LoggingInterface $logger, $ymlKey, $agentType, HttpRequest $httpRequest, HttpResponse $httpResponse) {
        $this->logger = $logger;
        $this->ymlKey = $ymlKey;
        $this->agentType = $agentType;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
     
    }

    public function render($data = array()) {

        return $data;
    }


    protected function buildHeaders() {
        $headers = $this->httpResponse->getHeaders();
        $headers[] = 'Content-Type: application/json';

        return $headers;
    }
}