<?php

namespace Application\Model\Factory;

use Application\Model\AuthModel;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        
        $config = $container->get('Config');
        
        if (isset($config['access_filter']))
        {
            $config = $config['access_filter'];
        }
        else
        {
            $config = [];
        }
        
        $request = $container->get('Request');
        
        return new AuthModel($authService, $config, $request);
    }
}