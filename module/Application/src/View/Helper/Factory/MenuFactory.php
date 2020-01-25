<?php
namespace Application\View\Helper\Factory;

use Application\View\Helper\Menu;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        
        return new Menu($authService);
    }
}