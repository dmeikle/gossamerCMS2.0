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
 * Date: 3/2/2017
 * Time: 11:07 PM
 */

namespace Gossamer\Horus\Filters;


use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;

class FilterDispatcher
{

    private $filterChain;

    private $logger;

    private $datasourceFactory;

    private $container;

    public function __construct(LoggingInterface $logger) {
        $this->filterChain = new FilterChain();
        $this->logger = $logger;
    }

    public function setContainer(Container $container) {
        $this->container = $container;
    }

    public function setDatasources(DatasourceFactory $datasourceFactory) {
        $this->datasourceFactory = $datasourceFactory;
    }

    public function setFilters(array $filterConfig) {

        foreach ($filterConfig as $filterParams) {
            $this->addFilter($filterParams);
        }
    }

    public function setFilterConfigurationPath($path, $keys = null) {

        $config = $this->loadConfig($path);

        if (!is_null($keys)) {
            $keyList = explode('.', $keys);
            foreach ($keyList as $key) {
                $config = $config[$key];
            }
        }
        $this->setFilters($config);
    }

    protected function addFilter($filterParams) {
        
        $filterName = $filterParams['filter'];
        $filter = null;

        if (class_exists($filterName)) {
            $filter = new $filterName($this->getFilterConfiguration($filterParams));
        } else {
            throw new \InvalidArgumentException("$filterName from bootstrap.yml does not exist");
        }

        $filter->setContainer($this->container);
        
        $this->filterChain->addFilter($filter);

    }

    protected function getFilterConfiguration(array $filterParams) {
        $filterConfig = new FilterConfig($filterParams);

        return $filterConfig;
    }

    /**
     * @param HttpRequest &$request
     * @param HttpInterface $response
     * @return bool
     * @throws \Exception
     *
     * runs through all filters. if the response->immediate_write is not false
     * then we found something to stop our processing of the request, and simply
     * output the response
     */
    public function filterRequest(HttpRequest &$request, HttpResponse &$response) {
        try {
            
            $result = $this->filterChain->execute($request, $response, $this->filterChain);

            if ($response->getAttribute(FilterChain::IMMEDIATE_WRITE) !== false) {
                return $response->getAttribute(FilterChain::IMMEDIATE_WRITE);
            }
        } catch (\Exception $e) {
            //die($e->getMessage());
            $this->logger->addError($e->getMessage());
            throw $e;
        }
        //successful completion
        return true;
    }

}