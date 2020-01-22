<?php

namespace Application\Controller\Factory;

use Application\Controller\AdministradorController;
use Application\Model\SalabModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdministradorControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $salabModel = $container->get(SalabModel::class);
        
        return new AdministradorController($salabModel);
    }
}
