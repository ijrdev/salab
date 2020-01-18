<?php

namespace Application\Controller\Factory;

use Application\Controller\LaboratoristaController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LaboratoristaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LaboratoristaController();
    }
}
