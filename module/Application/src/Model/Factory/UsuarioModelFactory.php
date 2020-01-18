<?php

namespace Application\Model\Factory;

use Application\Adapter\Db;
use Application\Model\UsuarioModel;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UsuarioModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $db = $container->get(Db::class);
        
        return new UsuarioModel($db);
    }
}