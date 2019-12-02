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
 * Time: 7:32 PM
 */

namespace Gossamer\Core\Services;

use Gossamer\Core\Datasources\DatasourceInterface;
use Gossamer\Horus\Http\HttpAwareInterface;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Pesedget\Entities\EntityManager;
use Gossamer\Set\Utils\Container;

/**
 * loads all bootstrap services
 *
 * @author Dave Meikle
 */
class ServiceManager
{

    private $serviceConfig = null;
    private $services = null;
    private $logger = null;
    private $datasourceFactory = null;
    private $container = null;
    private $entityManager = null;
    private $httpRequest = null;
    private $httpResponse = null;
    private $componentServices = null;

    /**
     * ServiceManager constructor.
     * @param LoggingInterface $logger
     * @param array $serviceConfig
     * @param array $componentServices
     * @param EntityManager $entityManager
     * @param Container $container
     */
    public function __construct(LoggingInterface $logger, array $serviceConfig, array $componentServices = null, EntityManager $entityManager, Container $container, HttpRequest $httpRequest, HttpResponse $httpResponse) {
       error_log('servicemanager');
        $this->container = $container;
        $this->formatArray($serviceConfig);
        $this->services = array();
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        if(!is_null($componentServices)) {
            $this->formatArray($componentServices);
        }
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    /**
     *
     * @param array $config
     */
    private function formatArray(array $config) {
        foreach ($config as $serviceKey => $serviceList) {

            error_log("key $serviceKey");
            foreach ($serviceList as $key => $item) {
                $this->serviceConfig[$serviceKey][$key] = $item;
            }
        }
    }

    /**
     * creates all services
     *
     * @param array $config
     *
     * @return HttpAwareInterface
     */
    private function assembleService(array $config) {

        $constructors = array();
        if (array_key_exists('constructor', $config)) {
            $constructors = $config['constructor'];
        }
        $injectors = array();

        //load any  constructor parameters
        foreach ($constructors as $key => $constructor) {
            $injectors = array_merge($injectors, $this->fetchServiceType($key, $constructor));
        }

        $className = $config['handler'];
        $class = new $className(...array_values($injectors));
error_log($className);
        if ($class instanceof ParametersInterface) {
            $parameters = array();
            if (array_key_exists('parameters', $config)) {
                foreach ($config['parameters'] as $key => $parameter) {
                    $parameters = array_merge($parameters, $this->fetchServiceType($key, $parameter));

                }

                $class->setParameters($parameters);
            }
        }
        if (array_key_exists('Gossamer\Set\Utils\ContainerTrait', class_uses($class))) {
            $class->setContainer($this->container);
        }
        //if(array class_uses($class))

        if (array_key_exists('datasource', $config) && $class instanceof DatasourceInterface) {

               // $conn = $this->datasourceFactory->getDatasource($config['datasource'], $this->logger);
                $conn = $this->entityManager->getConnection($config['datasource']);
                $class->setConnection($conn);

        }

        if ($class instanceof HttpAwareInterface) {

//            $class->setHttpRequest($this->container->get('HttpRequest'));
//            $class->setHttpResponse($this->container->get('HttpResponse'));

            $class->setHttpRequest($this->httpRequest);
            $class->setHttpResponse($this->httpResponse);
            $class->setLogger($this->logger);
        }

        return $class;
    }


    protected function fetchServiceType($key, $value) {
        $injector = array();

        if (substr($value, 0, 1) == '@') {
            $value = substr($value, 1);

            //it's another service - looks like we're starting a loop here...
            $injector[$value] = $this->getService($value);
        } elseif (substr($value, 0, 11) == 'container::') {
            $item = substr($value, 11);
            $injector[$key] = $this->container->get($item);
        } elseif ($value == 'httpRequest') { 
            $injector[$key] = $this->httpRequest;
        } elseif ($value == 'httpResponse') {
            $injector[$key] = $this->httpResponse;
        } elseif (substr($value, 0, 9) == 'container') {
            $injector[$key] = $this->container;
        } else {
            //load it like a class file - hopefully the author didn't expect any constructor params...
            $injector[$key] = new $value();
        }

        return $injector;
    }


    /**
     * getService - a lazy loader that will only create a service if asked for...
     *              no sense creating a bunch of services in config if the requested
     *              url doesn't require them
     * @param string $key - the key in the services.yml file
     * @return service
     */
    public function getService($key) {

        if (is_null($key)) {
            return;
        }

        if (!array_key_exists($key, $this->services)) {
            $this->services[$key] = $this->assembleService($this->serviceConfig[$key]);
        }

        return $this->services[$key];
    }

    /**
     * calls the execute() method of a service requested
     *
     * @param string $key
     *
     * @return mixed
     */
    public function executeService($key) {

        $service = $this->getService($key);

        if (is_null($service)) {
            return;
        }

        try {
            $service->execute();
        } catch (\Exception $e) {

        }
    }
}