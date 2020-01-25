<?php

namespace Application\Model\Factory;

use Application\Adapter\Db;
use Application\Model\AdministradorModel;
use Application\Model\AuthAdapter;
use Application\Model\SessionModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $db                  = $container->get(Db::class);
        $sessionModel        = $container->get(SessionModel::class);
        $administradornModel = $container->get(AdministradorModel::class);
                        
        return new AuthAdapter($db, $sessionModel, $administradornModel);
    }
}