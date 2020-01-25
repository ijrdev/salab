<?php

namespace Application\Controller\Factory;

use Application\Controller\AdministradorController;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdministradorControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $administradorModel = $container->get(AdministradorModel::class);
        $laboratoristaModel = $container->get(LaboratoristaModel::class);
        
        return new AdministradorController($administradorModel, $laboratoristaModel);
    }
}
