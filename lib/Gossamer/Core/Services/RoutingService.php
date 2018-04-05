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
 * Time: 4:06 PM
 */

namespace Gossamer\Core\Services;


use Gossamer\Core\Routing\DBRouter;
use Gossamer\Core\Routing\Router;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;

class RoutingService
{

    use \Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;

    public function getRouting(LoggingInterface $logger, HttpRequest &$httpRequest) {
        $router = $this->getRouterType($logger, $httpRequest);
        
        $currentNode = $router->getCurrentNode();

        return $currentNode;
    }

    private function getRouterType(LoggingInterface $logger, HttpRequest $httpRequest) {
        $config = $this->loadConfig($httpRequest->getSiteParams()->getConfigPath() . 'config.yml');
        if(!array_key_exists('server_context', $config)) {
            throw new ConfigurationNotFoundException('server_context missing from application config');
        }

        if($config['server_context'] === 'database') {
          
            return new DBRouter($logger, $httpRequest);
        }

        return new Router($logger, $httpRequest);
    }
}