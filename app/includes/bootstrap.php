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
use Gossamer\Ra\Security\Handlers\AuthorizationHandler;

$logger = buildLogger($siteParams);
$container = new \Gossamer\Set\Utils\Container();
$container->set('Logger', $logger);
$bootStrapLoader = new \Gossamer\Core\Kernel\BootstrapLoader();

//request is for all parameters needed to complete the request
$httpRequest = new \Gossamer\Horus\Http\HttpRequest($bootStrapLoader->getRequestParams(), $siteParams);
//can't use this here - we need to determine our yml key first
//$httpRequest = new Gossamer\Aset\Http\AsetHttpRequest($bootStrapLoader->getRequestParams(),$siteParams);

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

//check for immediate write here rather that in the filterchain - I don't like to see calls to write and exit buried deep within the code
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

/**
 * convert this to an AsetHttpRequest so we can filter our arguments passed according to nodeConfig
 * if the main app/config file is set to
 * security:
 *     variables:
 *          secure: true
 *
 * this is for basic casting of parameters into their datatypes before beginning the request_start event
 */
$siteConfig = $httpRequest->getSiteParams()->getSiteConfig();
if(isset($siteConfig['security']['variables']['secure']) && $siteConfig['security']['variables']['secure'] == '1') {
    $asetRequestFactory = new \Gossamer\Aset\Utils\RequestFactory();
    $httpRequest = $asetRequestFactory->getHttpRequest($httpRequest);
    unset($asetRequestFactory);
}

unset($routingService);

$eventDispatcher->setConfiguration($nodeConfig, $httpRequest->getRequestParams()->getYmlKey());

$container->set('EventDispatcher', $eventDispatcher);

//run any services (eg: firewall) before proceeding
$componentServices = (loadConfig($siteParams->getSitePath() . DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.yml'));
$serviceManager = new \Gossamer\Core\Services\ServiceManager($logger, loadConfig($siteParams->getConfigPath() . 'services.yml'), $componentServices, $entityManager, $container, $httpRequest, $httpResponse);

//$handler = new \Gossamer\Ra\Security\Handlers\AuthorizationHandler($container->get('Logger'), $httpRequest);
//$handler->setContainer($container);
//$handler->setParameters($container->get('securityContext'))


/**
 * Now that we have the YML key and node config for this request
 * check the authorization of the user to see if they are able
 * to make this request with the requested parameters.
 *
 * This is all stored in the security.yml file of the requested
 * component's config directory.
 *
 * eg:
 * components/members/config/security.yml
 *  members_get:
        access_control:
            #method options: uri, post, query
            subject:
                param: id <-- the id of a member to view
                method: uri <-- where to look for the parameter
            roles:
                - IS_MEMBER <-- list of allowable roles for this yml key
                - IS_MANAGER
            rules:
                - { class: 'Gossamer\Ra\Security\Authorization\Voters\CheckUserByRolesVoter', self: true, ignoreRolesIfNotSelf: [IS_MEMBER] }
 *
 *  ---------------- notes: ----------------
 *                      class - the class of the voter to instantiate
 *                      self - user gets a free pass if its their own id regardless of role
 *                      ignoreRolesIfNotSelf - if it's not a member's own id, disregard the IS_MEMBER role, they need more than that
 */
if($siteConfig['server_context'] == 'standard') {
    $serviceDispatcher = new \Gossamer\Core\Services\ServiceDispatcher($logger, $httpRequest, loadConfig($siteParams->getConfigPath() . 'firewall.yml'));
    $serviceDispatcher->dispatch($serviceManager, $httpRequest, $httpResponse);
    try {
        $authorizationHandler = $serviceManager->getService('authorization_handler');
        $authorizationHandler->execute();
    } catch (\Gossamer\Ra\Exceptions\UnauthorizedAccessException $e) {
        renderErrorResult($e);
    }
}

/**
 * Now that we have the node configuration loaded for this particular request,
 * let's see if there are any filters or events to fire off on entry_point
 */
//check to see if we need to run any filters for entry_point on the node config
//that is specific to this request
runFilters($httpRequest->getSiteParams()->getConfigPath() . 'filters.yml', 'all',\Gossamer\Horus\Filters\FilterEvents::FILTER_REQUEST_START);
runFilters($httpRequest->getSiteParams()->getSitePath(). DIRECTORY_SEPARATOR . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'filters.yml',
    $httpRequest->getRequestParams()->getYmlKey(),\Gossamer\Horus\Filters\FilterEvents::FILTER_REQUEST_START);

//check for immediate write here rather that in the filterchain - I don't like to see calls to write and exit buried deep within the code
if(!is_null($httpResponse->getAttribute(\Gossamer\Horus\Filters\FilterChain::IMMEDIATE_WRITE))){
    renderResult(array('data' => $httpResponse->getAttribute('data')));
}

//check to see if we need to run any events for entry_point on the node config
//that is specific to this request
if (file_exists($httpRequest->getSiteParams()->getSitePath() . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'listeners.yml')) {
    $requestListenersConfig = loadConfig($httpRequest->getSiteParams()->getSitePath() . $nodeConfig['componentPath'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'listeners.yml');
    try {
        if (!is_null($requestListenersConfig) && array_key_exists($httpRequest->getRequestParams()->getYmlKey(), $requestListenersConfig)) {
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

//$container->set('EventDispatcher', $eventDispatcher);
$container->set('FilterService', $filterService);


//now we return to the index.php file