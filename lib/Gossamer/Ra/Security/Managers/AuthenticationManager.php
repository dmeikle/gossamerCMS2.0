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
 * Date: 3/9/2017
 * Time: 12:18 AM
 */

namespace Gossamer\Ra\Security\Managers;

use Gossamer\Neith\Logging\LoggingInterface;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\HttpResponse;
use Gossamer\Ra\Exceptions\ClientCredentialsNotFoundException;
use Gossamer\Ra\Security\Client;
use Gossamer\Ra\Security\Providers\AuthenticationProviderInterface;
use Gossamer\Ra\Security\SecurityContextInterface;
use Gossamer\Ra\Security\SecurityToken;
use Gossamer\Set\Utils\Container;


/**
 * Class AuthenticationManager
 * @package Gossamer\Ra\Security\Managers
 *
 * This class is the abstract authentication class. It is intended for handling all
 * requests that require authentication before proceeding. A provider will be
 * passed in which does the work unique to the context of the request
 * eg: retrieving member information without the manager being aware of where the
 * information came from.
 * The manager then determines the validity of the requesting source based on
 * information provided by the provider
 */
abstract class AuthenticationManager
{

    use \Gossamer\Set\Utils\ContainerTrait;

    protected $logger;

    protected $userAuthenticationProvider = null;

    protected $container;

    protected $node;

    protected $request;

    protected $response;

    public function __construct(LoggingInterface $logger, HttpRequest &$request, Container $container, AuthenticationProviderInterface $provider, HttpInterface $response = null) {
        $this->logger = $logger;
        $this->request = $request;
        $this->container = $container;
        $this->userAuthenticationProvider = $provider;
        $this->response = $response;

    }


    /**
     * authenticates a user based on their context
     *
     * @param \core\components\security\core\SecurityContextInterface $context
     *
     * @throws ClientCredentialsNotFoundException
     */
    public function authenticate(SecurityContextInterface &$context) {

        $token = $this->generateEmptyToken($this->getSession());

        try {
            $client = $this->userAuthenticationProvider->loadClientByCredentials($token->getClient());

            $post = $this->request->getRequestParams()->getPost();
            if(array_key_exists('password', $post)) {
                if(!$this->comparePasswords($post['password'], $this->getClientPassword($client))) {
                    throw new ClientCredentialsNotFoundException('Client not found with provided credentials');
                }
            }



            //this was a fix in a child class - may be needed here
           // $token = $this->generateNewToken(new Client($client));
            $token = $this->generateNewToken($client);

           
        } catch (ClientCredentialsNotFoundException $e) {

            $this->logger->addAlert('Client not found ' . $e->getMessage());
            throw $e;
        }

        //validate the client, if good then add to the context
        if (true) {
            $context->setToken($token);
        }

        setSession('_security_secured_area', serialize($token));
    }



    protected function getClientPassword($client) {
        if(is_array($client)) {
            return $client['password'];
        }

        return $client->getPassword();
    }
    /**
     * generates a default token
     *
     * @return SecurityToken
     */
    public function generateEmptyToken($session) {

        $token = @unserialize($session['_security_secured_area']);

        if (!$token) {

//            echo debug_backtrace()[0]['class']."<br>\r\n";
//            echo debug_backtrace()[0]['function']."<br>\r\n";
//            echo debug_backtrace()[1]['function']."<br>\r\n";
//            echo debug_backtrace()[2]['function']."<br>\r\n";
//            echo debug_backtrace()[3]['function']."<br>\r\n";
//            echo debug_backtrace()[4]['function']."<br>\r\n";

            return $this->generateNewToken();
        }

        return $token;
    }


    /**
     * generates a new token based on current client.
     *
     * can pass an optional client in, in case we just logged in and need to update the token
     * with new client details
     *
     * @param Client|null $client
     * @return SecurityToken
     */
    public function generateNewToken(Client $client = null) {

        if (is_null($client)) {

            $client = $this->getClient();

        }

        $token = new SecurityToken($client, $this->request->getRequestParams()->getYmlKey(), $client->getRoles());

        return $token;
    }

    /**
     *
     * @return \Gossamer\Ra\Security\Client
     */
    public abstract function getClient();

    /**
     * retrieves a list of credentials (IS_ADMINISTRATOR|IS_ANONYMOUS...)
     *
     * @return array(credentials)|null
     */
    protected abstract function getClientHeaderCredentials();

    /**
     * @return mixed
     */
    //protected abstract function getSession();
    protected function getSession() {

        return $_SESSION;
    }

    protected function comparePasswords($password, $encrypted) {
        //used with password_hash("mynewpassword", PASSWORD_DEFAULT);
        return (password_verify($password, $encrypted));
    }
}