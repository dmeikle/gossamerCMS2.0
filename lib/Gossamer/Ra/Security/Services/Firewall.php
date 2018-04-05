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
 * Date: 10/29/2017
 * Time: 2:17 PM
 */

namespace Gossamer\Ra\Security\Services;


use Gossamer\Core\Routing\URISectionComparator;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Neith\Logging\LoggingInterface;

class Firewall
{

    private $logger = null;

    private $httpRequest = null;

    /**
     *
     * @param Logger $logger
     */
    public function __construct(LoggingInterface $logger, HttpRequest $httpRequest) {
        $this->logger = $logger;
        $this->httpRequest = $httpRequest;
    }

    public function getFirewallKey(array $firewallConfig) {

        $comparator = new URISectionComparator(null, $this->httpRequest);
        $config = $comparator->findPattern($firewallConfig, $this->httpRequest->getRequestParams()->getUri());
      
        if ($config === false) {

            return null;
        }
        $serviceConfig = current($config);

        if (array_key_exists('authentication', $serviceConfig) && !$this->checkIsIgnored($serviceConfig)) {

            return $serviceConfig['authentication'];
        }

        return null;
    }

    private function checkIsIgnored(array $nodeConfig) {

        if(array_key_exists('ignore', $nodeConfig)) {
            if(in_array($this->httpRequest->getRequestParams()->getUri(), $nodeConfig['ignore'])) {
                //tell firewall to ignore this
                return true;
            }
        }

        return false;
    }
}