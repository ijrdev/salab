<?php

namespace Application\Model\Factory;

use Application\Adapter\Db;
use Application\Model\AdministradorModel;
use Application\Model\AuthModel;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService        = $container->get(AuthenticationService::class);
        $config             = $container->get('Config');
        $db                 = $container->get(Db::class);
        $administradorModel = $container->get(AdministradorModel::class);
        
        return new AuthModel($authService, $config, $db, $administradorModel);
    }
}