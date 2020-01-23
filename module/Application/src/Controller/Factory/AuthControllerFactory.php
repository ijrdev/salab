<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Model\AuthModel;
use Application\Model\SalabModel;
use Application\Model\SessionModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionModel = $container->get(SessionModel::class);
        $authModel    = $container->get(AuthModel::class);
        $salabModel   = $container->get(SalabModel::class);
        
        return new AuthController($authModel ,$sessionModel, $salabModel);
    }
}
