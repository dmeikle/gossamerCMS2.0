<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Ra\Security\Handlers;


use Gossamer\Core\Configuration\Traits\LoadConfigurationTrait;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Set\Utils\ContainerTrait;

/**
 * AuthorizationHandler - placeholder - not implemented
 *
 * @author Dave Meikle
 */
abstract class AuthorizationHandler  {

    use ContainerTrait;
    use LoadConfigurationTrait;


    protected $container = null;
    protected $authorizationManager = null;
    protected $httpRequest;

    public function __construct(LoggingInterface $logger, HttpRequest $httpRequest) {
        $this->logger = $logger;
        $this->httpRequest = $httpRequest;
    }

    public function execute() {
        $securityConfig = $this->loadSecurityConfig();
        //check to see if we need to do anything
        if($securityConfig === false) {
            return;
        }

        //$this->container->set('securityContext', $this->securityContext);


        $token = $this->getToken();



        try {
            $this->authorizationManager->execute($securityConfig);
        } catch (\Exception $e) {


            if(array_key_exists('fail_url', $this->node)) {
                header('Location: ' . $this->getSiteURL() . $this->node['fail_url']);
            } else{
                echo json_encode(array('message' => $e->getMessage(), 'code' => $e->getCode()));
            }

            die();
        }

        //this is handled in the UserLoginManager
        //$this->container->set('securityContext', $this->securityContext);
    }




    /**
     * accessor
     *
     * @param array $params
     */
    public function setParameters(array $params) {

        $this->securityContext = $params['security_context'];
    }

    protected function loadSecurityConfig() {
        $config = array_shift($this->httpRequest->getNodeConfig());

        $path = $this->httpRequest->getSiteParams()->getSitePath() . DIRECTORY_SEPARATOR . $config['component_path'] .
            'config' . DIRECTORY_SEPARATOR . 'security.yml';

        $securityConfig = $this->loadConfig($path);

        if(array_key_exists($this->httpRequest->getYmlKey(), $securityConfig)) {
            return $securityConfig[$this->httpRequest->getYmlKey()];
        }

        return false;
    }
}
