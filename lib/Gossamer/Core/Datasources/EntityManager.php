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
 * Date: 3/8/2017
 * Time: 7:17 PM
 */

namespace Gossamer\Core\Datasources;


class EntityManager
{

    private static $manager = null;

    private $connections = array();

    private $config;

    private $defaultConnection;

    /**
     * expects the credentials.yml file passed in
     *
     * EntityManager constructor.
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }


    /**
     * does not instantiate, just recognize the default node
     */
    private function setDefaultConnection() {
        if (!array_key_exists('default', $this->config)) {
            throw new \RuntimeException('default key not specified in db credentials configuration');
        }

        $this->defaultConnection = $this->config['default'];
    }

    /**
     * instantiates and returns a requested connection
     * @param null $dbKey
     * @return mixed
     * @throws \Exception
     */
    public function getConnection($dbKey = null) {
        echo "here is config\r\n";
        print_r($this->config);
        echo "end config\r\n";
        if (is_null($dbKey)) {
            if (is_null($this->defaultConnection)) {
                throw new \Exception('dbkey not passed and no default key specified in entity manager');
            }

            $dbKey = $this->defaultConnection;
        }

        if (!array_key_exists($dbKey, $this->config)) {
            throw new \Exception('dbkey does not exist in entity manager collection');
        }

        return $this->_getConnection($dbKey);
    }

    /**
     * @param $dbKey
     * @return mixed
     */
    private function _getConnection($dbKey) {

        //check to see if it's already instantiated - if not, lets make it
        if (!array_key_exists($dbKey, $this->connections) || !is_object($this->connections[$dbKey])) {
           
            $dbClass = $this->config[$dbKey]['class'];
            $credentials = $this->config[$dbKey]['credentials'];

            $this->connections[$dbKey] = new $dbClass($credentials);
        }

        return $this->connections[$dbKey];
    }


    /**
     * @return mixed
     */
    public function getKeys() {
        return array_keys($this->connections);
    }


    /**
     * @param null $dbKey
     * @return mixed
     * @throws \Exception
     */
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
}