<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Database;

use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Horus\Http\HttpRequest;
use Monolog\Logger;
use Gossamer\Essentials\Configuration\YamlLoader;

/**
 * builds an instance of a connection and stores it for further use to avoid
 * redundant connection creation and handshakes
 * 
 */
class DatasourceFactory {

    use LoadConfigurationTrait;

    private $httpRequest;

    public function __construct(HttpRequest $httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    private $datasources = null;

    /**
     * 
     * @param type $sourceName
     * @param Logger $logger
     * 
     * @return REST|MYSQL|MSSQL connection objects
     * 
     * @throws \Exception
     */
    public function getDatasource($sourceName, Logger $logger) {
        $datasources = $this->getDatasources();

        if (!array_key_exists($sourceName, $datasources) || !is_object($datasources[$sourceName])) {
            try {
                $ds = $this->buildDatasourceInstance($sourceName, $logger);
                $datasources[$sourceName] = $ds;
            } catch (\Exception $e) {
                echo $e->getMessage();
           
                $logger->addError($sourceName . ' is not a valid datasource');
                throw new \Exception($sourceName . ' is not a valid datasource', 580);
            }
        }

        return $datasources[$sourceName];
    }

    /**
     * 
     * @return REST|MYSQL|MSSQL connection objects
     */
    private function getDatasources() {
        if (is_null($this->datasources)) {
            $this->datasources = array();
        }

        return $this->datasources;
    }

    /**
     * creates a datasource based on the credentials.yml configuration
     * 
     * @param type $sourceName
     * @param Logger $logger
     * 
     * @return \core\datasources\datasourceClass
     */
    private function buildDatasourceInstance($sourceName, Logger $logger) {

        $dsConfig = $this->loadConfig($this->httpRequest->getSiteParams()->getConfigPath() . 'credentials.yml');

        $datasourceClass = $dsConfig[$sourceName]['class'];

        $datasource = new $datasourceClass($dsConfig[$sourceName]['credentials']);

        return $datasource;
    }

}
