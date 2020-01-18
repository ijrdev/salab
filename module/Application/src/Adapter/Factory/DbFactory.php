<?php

namespace Application\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Application\Adapter\Db;

class DbFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = $container->get('configuration');
        
        return new Db($configuration['connections']);
    }
}