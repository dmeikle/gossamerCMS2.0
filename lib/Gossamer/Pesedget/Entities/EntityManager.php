<?php

namespace Gossamer\Pesedget\Entities;


/**
 * Description of EntityManager
 *
 * @author davem
 */
class EntityManager {

    private static $manager = null;
    private $connections = array();
    private $defaultConnection = null;
    private $config;
    private $entityList = null;


    public function __construct(array $credentialsConfig) {
        $this->config = $credentialsConfig;
        $this->setDefaultConnection();
    }

    public function __destruct() {
        $this->defaultConnection = null;
        $this->entityList = null;
        self::$manager = null;
    }

    private function setDefaultConnection() {
        if (!array_key_exists('default', $this->config)) {
            throw new \RuntimeException('default key not specified in db credentials configuration');
        }

        $this->defaultConnection = $this->config['default'];
    }


    public function getConnection($dbKey = null) {
        if(is_array($dbKey)) {
            $dbKey = $dbKey['entity_db'];
        }
        if (is_null($dbKey)) {
            if (is_null($this->defaultConnection)) {
                throw new \Exception('dbkey not passed and no default key specified in entity manager');
            }

            $dbKey = $this->defaultConnection;
        }

        if (!array_key_exists($dbKey, $this->config)) {


            throw new \Exception('dbkey ' . $dbKey . ' does not exist in entity manager collection');
        }

        return $this->_getConnection($dbKey);
    }

    private function _getConnection($dbKey) {
        if (!array_key_exists($dbKey, $this->connections) || !is_object($this->connections[$dbKey])) {
            $dbClass = $this->config[$dbKey]['class'];
            $credentials = $this->config[$dbKey]['credentials'];

            $this->connections[$dbKey] = new $dbClass($credentials);
        }

        return $this->connections[$dbKey];
    }


    public function getCredentials($dbKey = null) {
        if (is_null($dbKey)) {
            if (is_null($this->defaultConnection)) {
                throw new \Exception('dbkey not passed and no default key specified in entity manager');
            }

            $dbKey = $this->defaultConnection;
        }

        if (!array_key_exists($dbKey, $this->config)) {
            throw new \Exception('dbkey does not exist in entity manager credentials');
        }

        $config = $this->config[$dbKey];

        return $config['credentials'];
    }

    public function getKeys() {
        return array_keys($this->connections);
    }

    public function getDefaultConnection() {
        return $this->getConnection($this->config['default']);
    }
}
