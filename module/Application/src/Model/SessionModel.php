<?php

namespace Application\Model;

use Laminas\Session\SessionManager;

class SessionModel
{
    private $sessionManager;
    private $sessionContainer;

    public function __construct(SessionManager $sessionManager, $sessionContainer = []) 
    {
        $this->sessionManager   = $sessionManager;
        $this->sessionContainer = $sessionContainer;
    }
    
    public function getUsuario($field = null)
    {
        $session = $this->sessionContainer['usuario']['login'];
        
        if(!empty($session))
        {
            return $session;
        }
        
        if($field !== null)
        {
            return isset($this->sessionContainer['usuario']['login'][$field]) ? $this->sessionContainer['usuario']['login'][$field] : '';
        }
        
        return [];
    }
    
    public function setUsuario($dados)
    {
        return $this->sessionContainer['usuario']['login'] = $dados;
    }
    
    public function destroySessionContainer()
    {
        $this->sessionManager->destroy();
    }
    
}
