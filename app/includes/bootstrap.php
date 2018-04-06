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
 * Time: 8:25 PM
 */
$logger = buildLogger($siteParams);
$container = new \Gossamer\Set\Utils\Container();
$container->set('Logger', $logger);
$bootStrapLoader = new \Gossamer\Core\Kernel\BootstrapLoader();

//request is for all parameters needed to complete the request
$httpRequest = new \Gossamer\Horus\Http\HttpRequest($bootStrapLoader->getRequestParams(), $siteParams);
//$httpRequest = new extensions\aset\http\HttpAsetRequest($bootStrapLoader->getRequestParams(),$siteParams);

$entityManager = $bootStrapLoader->getEntityManager($httpRequest->getSiteParams()->getConfigPath());
$container->set('EntityManager', $entityManager);
$cacheManager = new \Gossamer\Caching\CacheManager($logger);
$cacheManager->setHttpRequest($httpRequest);
$container->set('CacheManager', $cacheManager);

//response if for all parameters to be sent out upon completion of request.
//place all values intended for 'sending' in here
$httpResponse = new \Gossamer\Horus\Http\HttpResponse();


//create the event dispatcher before filter service so that we can use it in event of error during filter chain
$dispatchService = new \Gossamer\Core\Services\DispatchService($logger, new \Gossamer\Essentials\Configuration\YamlLoader());
$dispatchService->setHttpRequest($httpRequest);
$dispatchService->setHttpResponse($httpResponse);
$eventDispatcher = $dispatchService->getEventDispatcher(loadConfig($siteParams->getConfigPath() . 'bootstrap.yml'), $container);




//run through our filters to see if we qualify the request to continue
$filterService = new Gossamer\Horus\Filters\FilterDispatcher($logger);
$filterService->setContainer($container);

//execute any entrypoint filters
runFilters($siteParams->getConfigPath() . 'filters.yml', 'all',\Gossamer\Horus\Filters\FilterEvents::FILTER_ENTRY_POINT);
if(!is_null($httpResponse->getAttribute(\Gossamer\Horus\Filters\FilterChain::IMMEDIATE_WRITE))){
    renderResult(array('data' => $httpResponse->getAttribute('data')));
}

/**
 * this is our first event called. We've finished bootstrapping, so let's begin entry point
 */
$event = new \Gossamer\Horus\EventListeners\Event(\Gossamer\Core\System\KernelEvents::ENTRY_POINT);
$eventDispatcher->dispatch('all', \Gossamer\Core\System\KernelEvents::ENTRY_POINT, $event);

//sets it inside the httpRequest object
$routingService = new \Gossamer\Core\Services\RoutingService();

//inside this getRouting call is where we determine the yml key
$nodeConfig = $routingService->getRouting($logger, $httpRequest);

//now we can finally set the yml key for this request
$httpRequest->getRequestParams()->setYmlKey($nodeConfig['ymlKey']);
//$httpRequest->getRequestParams()->setYmlKey(array_keys($nodeConfig));


unset($routingService);

$eventDispatcher->setConfiguration($nodeConfig, $httpRequest->getRequestParams()->getYmlKey());


//run any services (eg: firewall) before proceeding
$componentServices = (loadConfig($siteParams->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.yml'));
$serviceManager = new \Gossamer\Core\Services\ServiceManager($logger, loadConfig($siteParams->getConfigPath() . 'services.yml'), $componentServices, $entityManager, $container);


$serviceDispatcher = new \Gossamer\Core\Services\ServiceDispatcher($logger, $httpRequest, loadConfig($siteParams->getConfigPath() . 'firewall.yml'));
$serviceDispatcher->dispatch($serviceManager, $httpRequest, $httpResponse);

/**
 * Now that we have the node configuration loaded for this particular request,
 * let's see if there are any filters or events to fire off on entry_point
 */
//check to see if we need to run any filters for entry_point on the node config
//that is specific to this request

runFilters($httpRequest->getSiteParams()->getConfigPath() . 'filters.yml', 'all',\Gossamer\Horus\Filters\FilterEvents::FILTER_REQUEST_START);
runFilters($httpRequest->getSiteParams()->getSitePath(). DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'filters.yml',
    $httpRequest->getRequestParams()->getYmlKey(),\Gossamer\Horus\Filters\FilterEvents::FILTER_REQUEST_START);

//check to see if we need to run any events for entry_point on the node config
//that is specific to this request
if (file_exists($httpRequest->getSiteParams()->getSitePath() . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'listeners.yml')) {
    $requestListenersConfig = loadConfig($httpRequest->getSiteParams()->getSitePath() . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'listeners.yml');
    try {
        if (array_key_exists($httpRequest->getRequestParams()->getYmlKey(), $requestListenersConfig)) {
            $eventDispatcher->setConfiguration($requestListenersConfig[$httpRequest->getRequestParams()->getYmlKey()]);

            $result = $filterService->filterRequest($httpRequest, $httpResponse);

            if (is_array($result)) {
                renderResult($result);
            }

            $eventDispatcher->dispatch($httpRequest->getRequestParams()->getYmlKey(), \Gossamer\Core\System\KernelEvents::ENTRY_POINT, $event);
        }
    } catch (\Exception $e) {

    }
}

$container->set('EventDispatcher', $eventDispatcher);
$container->set('FilterService', $filterService);


//now we return to the index.php file