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


use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;

/**
 * this class handles all authentication when a user logs in. No need to create
 * a function inside a controller (don't waste time looking for it there, like
 * I sometimes forget a year later.. haha).
 *
 * Configuration is handled in the security.yml file
 * step 1:
 * create a manager that will be called during startup by the services manager.
 * This configuration is stored in the services.yml file.
 * eg:

  authentication_manager:
  handler: 'core\components\security\core\AuthenticationManager'
  'arguments':
  - '@user_authentication_provider'
  #the '@' sign means it's a service already configured.
  #no '@' sign means you specify the relative path to the file to load

 * step 2:
 * create a provider that can be passed into the manager to do the work.
 * Different providers can be directed to perform differently based on
 * yml file configuration.
 * eg:

  user_authentication_provider:
  handler: 'core\components\security\providers\UserAuthenticationProvider'
  datasource: datasource3
 *
 * the services manager will create the UserAuthenticationProvider and pass in
 * the datasource specified by yml key. Then it will create the
 * AuthenticationManager and pass the provider into it. The work is done by
 * the provider (which database, which checks to perform) - the manager just
 * orchestrates the calls.
 *
 * step 3:
 * create a reference that will define the handler to use the manager and the
 * provider inside the services.yml file
 *
  simple_auth:
  'handler': 'core\components\security\handlers\AuthenticationHandler'
  #3 is the local db conn wrapped in a connection adapter
  'datasource': 'datasource3'
  'arguments':
  security_context: '@security_context'
  authentication_manager: '@authentication_manager'
 *
 * step 4:
 * create the rule that calls all of this in firewall.yml :
 *
 * admin_area:
  pattern: /admin
  authentication: simple_auth
  fail_url: admin/login
 *
 *
 * in a nutshell:
 * 1.   create a provider and specify any passed in objects
 * 2.   create a manager and specify any passed in objects, including the provider
 *      to use in this context
 * 3.   create a handler and specify the manager to use
 * 4.   create a firewall reference and tell it which handler to call when the
 *      matching URI pattern occurs
 *
 * @author Dave Meikle
 */
abstract class AuthenticationHandler  {

    protected $securityContext = null;
    protected $authenticationManager = null;
    protected $logger = null;
    protected $container = null;
    protected $node = null;

    const FIREWALL_CACHE_KEY = 'FIREWALL_RULES';
    /**
     *
     * @param Logger $logger
     */
    public function __construct(LoggingInterface $logger, Container $container) {
        $this->logger = $logger;
        $this->container = $container;

        $this->loadNodeConfig();
    }



    /**
     * main method called. calls the provider and gets the provider to
     * authenticate the user
     *
     * @return type
     */
    public function execute() {
        $this->container->set('securityContext', $this->securityContext);

        if (is_null($this->node) || !array_key_exists('authentication', $this->node)) {
            return;
        }
        if (array_key_exists('security', $this->node) && (!$this->node['security'] || $this->node['security'] == 'false')) {
            error_log('security element null or not found in node');
            return;
        }

           $token = $this->getToken();
      

        
        try {
           
            $this->authenticationManager->authenticate($this->securityContext);
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
     * @return string

    protected function getSiteURL() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';

        return $protocol . $domainName;
    }
*/

    /**
     * loads the firewall configuration
     *
     * @return empty|array
     */
    abstract protected function loadNodeConfig(HttpRequest $httpRequest);



    /**
     * accessor
     *
     * @param array $params
     */
    public function setParameters(array $params) {

        $this->securityContext = $params['security_context'];
        $this->authenticationManager = $params['authentication_manager'];
    }

    /**
     * accessor
     *
     * @return SecurityToken
     */
    protected function getToken() {

        return $this->authenticationManager->generateEmptyToken();
    }

}
