<?php

namespace Application\Model\Factory;

use Application\Model\SessionModel;
use Interop\Container\ContainerInterface;
use Laminas\Session\SessionManager;

class SessionModelFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $sessionManager = $container->get(SessionManager::class);
        $config         = $container->get('Config');
        
        $sessionContainer = [];
        
        try
        {
            if (isset($config['session_containers']) && !empty($config['session_containers']))
            {
                foreach ($config['session_containers'] as $serviceName)
                {
                    $sessionContainer[$serviceName] = $container->get($serviceName);
                }
            }
        }
        catch (\Exception $exc){}
        
        return new SessionModel($sessionManager, $sessionContainer);
    }
}