<?php

namespace Application\Controller\Factory;

use Application\Controller\AdministradorController;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdministradorControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService        = $container->get(AuthenticationService::class);
        $administradorModel = $container->get(AdministradorModel::class);
        $laboratoristaModel = $container->get(LaboratoristaModel::class);
        
        return new AdministradorController($authService, $administradorModel, $laboratoristaModel);
    }
}
