<?php

namespace Application\Controller\Factory;

use Application\Controller\LaboratoristaController;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LaboratoristaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionModel       = $container->get(SessionModel::class);
        $laboratoristaModel = $container->get(LaboratoristaModel::class);
        $administradorModel = $container->get(AdministradorModel::class);
        
        return new LaboratoristaController($sessionModel, $laboratoristaModel, $administradorModel);
    }
}
