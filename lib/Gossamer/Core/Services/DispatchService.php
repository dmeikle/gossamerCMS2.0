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
 * Time: 9:09 PM
 */

namespace Gossamer\Core\Services;



use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;

class DispatchService extends AbstractService
{

    
    public function getEventDispatcher(array $config, Container $container) {
        $dispatcher = new EventDispatcher($this->logger, $this->httpRequest,$this->httpResponse,$this->httpRequest->getMethod(), $this->httpRequest->getYmlKey(), $this->getBootstrapConfig());

        $dispatcher->setContainer($container);
        $dispatcher->setDatasources(new DatasourceFactory(), $this->getDatasourceConfig());
        $dispatcher->setConfiguration($this->getBootstrapConfig(), 'all');

        return $dispatcher;
    }
    
    private function getBootstrapConfig() {

        return $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'bootstrap.yml');
    }

    private function getDatasourceConfig() {

        return $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'credentials.yml');
    }
}