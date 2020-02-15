<?php

namespace Application\Model\Factory;

use Application\Adapter\Db;
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
        
        $db                 = $container->get(Db::class);
        $administradorModel = $container->get(\Application\Model\AdministradorModel::class);
        
        return new AuthModel($authService, $config, $db, $administradorModel);
    }
}