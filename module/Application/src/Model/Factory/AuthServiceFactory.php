<?php

namespace Application\Model\Factory;
//use Laminas\Authentication\Storage\Session;


use Application\Model\AuthAdapter;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;

class AuthServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Zend\Authentication\AuthenticationService service 
     * and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage    = new Session(null, null, $sessionManager);
        $authAdapter    = $container->get(AuthAdapter::class);
        
        return new AuthenticationService($authStorage, $authAdapter);
    }
}