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
 * Time: 11:08 PM
 */

namespace Gossamer\Horus\Filters;

 
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Pesedget\Database\DatasourceFactory;
use Gossamer\Set\Utils\Container;

class AbstractFilter
{
    protected $datasourceFactory;

    protected $filterConfig;

    protected $container;

    protected $params = null;

    protected $httpRequest;

    protected $httpResponse;

    const METHOD_DELETE = 'delete';
    const METHOD_SAVE = 'save';
    const METHOD_PUT = 'put';
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const VERB_LIST = 'list';
    const VERB_DELETE = 'delete';
    const VERB_GET = 'get';
    const VERB_SEARCH = 'search';
    const VERB_SAVE = 'save';
    const DIRECTIVES = 'directives';

    /**
     * AbstractFilter constructor.
     * @param FilterConfig $config
     * @param array|null $params
     */
    public function __construct(FilterConfig $config, array $params = null) {
        $this->filterConfig = $config;
        $this->params = $params;
    }

    public function setHttpRequest(HttpRequest $httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    public function setHttpResponse(HttpResponse $httpResponse) {
        $this->httpResponse = $httpResponse;
    }
    /**
     * @param DatasourceFactory $datasourceFactory
     */
    public function setDatasourceFactory(DatasourceFactory $datasourceFactory) {
        $this->datasourceFactory = $datasourceFactory;
    }

    protected function getEntityManager() {
        return $this->container->get('EntityManager');
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container) {
        $this->container = $container;
    }

    /**
     * @param HttpRequest &$request
     * @param HttpInterface $response
     * @param FilterChain $chain

    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {
        try {

            $chain->execute($request, $response, $chain);
        } catch (\Exception $e) {
 
        }
    }
*/
    public function execute(HttpRequest &$request, HttpResponse &$response, FilterChain &$chain) {
        try {

            $chain->execute($request, $response, $chain);
        } catch (\Exception $e) {
 
        }
    }
}