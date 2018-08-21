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
 * Date: 10/29/2017
 * Time: 2:58 PM
 */

namespace Gossamer\Ra\Security\Services;


use Gossamer\Horus\EventListeners\Event;
use Gossamer\Ra\Exceptions\ClientCredentialsNotFoundException;
use Gossamer\Ra\Security\Managers\AuthenticationManager;
use Gossamer\Ra\Security\SecurityContextInterface;

class AuthenticationService
{
    private $authenticationManager = null;

    private $securityContext = null;
    
    public function __construct(AuthenticationManager $authenticationManager, SecurityContextInterface $securityContext) {
        $this->authenticationManager = $authenticationManager;
        $this->securityContext = $securityContext;
    }

    public function execute() {

        try {
            $this->authenticationManager->authenticate($this->securityContext);
        }catch(ClientCredentialsNotFoundException $e) {

           renderResult(array(
               'headers' => array(),
               'data'=> array('code' => '401', 'message'=>'Client credentials not found')
           ));
        }catch(\Exception $e){
           echo "error occurred in Authentication Service\r\n";
            echo $e->getMessage();
            die($e->getMessage());
        }



        setSession('_security_secured_area', serialize($this->securityContext->getToken()));

    }
}