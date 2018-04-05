<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Ra\Security\Handlers;


use Gossamer\Neith\Logging\LoggingInterface;

/**
 * AuthorizationHandler - placeholder - not implemented
 *
 * @author Dave Meikle
 */
abstract class AuthorizationHandler  {

    protected $container = null;
    protected $provider = null;

    public function __construct(LoggingInterface $logger, Container $container) {
        $this->logger = $logger;
        $this->container = $container;
    }

    public abstract function execute() ;



    public function setParameters(array $params) {
        $this->provider = $params['provider'];
    }

}
