<?php

namespace Application\Model;

use Laminas\Authentication\AuthenticationService;

class AuthModel
{
    private $authService;
    private $config;
    private $request;
    
    public function __construct(
            AuthenticationService $authService,
            $config  = [],
            $request = []
    )
    {
        $this->authService = $authService;
        $this->config      = $config;
        $this->request     = $request;
    }

    public function login($matricula, $senha)
    {
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setMatricula($matricula);
        $authAdapter->setSenha($senha);
        $result = $this->authService->authenticate();
        
        return $result;
    }

    public function logout()
    {
        if($this->authService->getIdentity() != null)
        {
            $this->authService->clearIdentity();
        }
    }
    
    public function filterAccess($controllerName, $actionName)
    {      
        $mode = isset($this->config['options']['mode']) ? $this->config['options']['mode'] : 'restrictive';
        
        if($mode != 'restrictive' && $mode != 'permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');

        if(isset($this->config['controllers'][$controllerName])) 
        {
            $items = $this->config['controllers'][$controllerName];
            
            foreach($items as $item) 
            {
                $actionList = isset($item['actions']) ? $item['actions'] : '*';
                $allow = $item['allow'];
                
                if(is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList == '*') 
                {
                    if($allow == '*')
                        return true; // Anyone is allowed to see the page.
                    else if($allow == '@' && $this->authService->hasIdentity()) 
                    {
                        return true; // Only authenticated user is allowed to see the page.
                    } else 
                    {                    
                        return false; // Access denied.
                    }
                }
            }            
        }

        // In restrictive mode, we forbid access for authenticated users to any 
        // action not listed under 'access_filter' key (for security reasons).
        if($mode == 'restrictive' && !$this->authService->hasIdentity())
            return false;

        // Permit access to this page.
        return true;
    }
}