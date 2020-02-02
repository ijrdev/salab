<?php

namespace Application\Controller\Factory;

use Application\Controller\ProfessorController;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProfessorControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionModel       = $container->get(SessionModel::class);
        $laboratoristaModel = $container->get(LaboratoristaModel::class);
        $administradorModel = $container->get(AdministradorModel::class);
        
        return new ProfessorController($sessionModel, $laboratoristaModel, $administradorModel);
    }
}
