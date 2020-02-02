<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Menu extends AbstractHelper
{
    private $authService;
    private $authUser;
    private $items         = [];
    private $activeItem    = '';
    private $activeSubItem = '';
    
    public function __construct(\Laminas\Authentication\AuthenticationService $authService) 
    {
        $this->authService = $authService;
    }

    public function setUser()
    {
        $this->authUser = $this->authService->getIdentity()['id_grupo'];
    }
    
    public function setItems() 
    {
        $this->setUser();
        
        if(isset($this->authUser) && !empty($this->authUser))
        {
            switch($this->authUser)
            {
                case 1:
                    $this->items = [
                        'Início'   => '/administrador',
                        'Reserva' => '/administrador/reserva',
                        'Cadastrar' => [
                            'Usuário'     => '/administrador/cadastrar-usuario',
                            'Laboratório' => '/administrador/cadastrar-laboratorio'
                        ],
                        'Consultar' => [
                            'Usuário'     => '/administrador/consultar-usuarios',
                            'Laboratório' => '/administrador/consultar-laboratorios'
                        ],
                        'Aviso'  => '/administrador/aviso',
                        'Perfil' => '/administrador/perfil',
                        'Sair'   => '/logout',
                    ];
                    break;
                case 2:
                    $this->items = [
                        'Início'       => '/laboratorista',
                        'Reserva'      => '/laboratorista/reserva',
                        'Laboratórios' => '/laboratorista/laboratorios',
                        'Aviso'        => '/laboratorista/aviso',
                        'Perfil'       => '/laboratorista/perfil',
                        'Sair'         => '/logout'
                    ];
                    break;
                case 3:
                    $this->items = [
                        'Início'          => '/professor',
                        'Minhas Reservas' => '/professor/minhas-reservas',
                        'Agendar'         => '/professor/agendar',
                        'Aviso'           => '/professor/aviso',
                        'Perfil'          => '/professor/perfil',
                        'Sair'            => '/logout'
                    ];
                    break;
            }
        }
    }
    
    public function setActiveItem($activeItem) 
    {
        $this->activeItem = $activeItem;
    }
    
    public function setActiveSubItem($activeSubItem) 
    {
        $this->activeSubItem = $activeSubItem;
    }
    
    public function render()
    {
        $this->setItems();
        
        if(empty($this->items))
        {
            return '';
        }
        
        $menu = "<ul class='navbar-nav'>";
        
        foreach($this->items as $item => $url) 
        {
            if($item == $this->activeItem)
            {
                if(is_array($url))
                {
                    $menu .= 
                        "<li class='nav-item dropdown active'>
                            <a id='dropdownSubMenu' href='' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' class='nav-link dropdown-toggle'>$item</a>
                                <ul aria-labelledby='dropdownSubMenu' class='dropdown-menu border-0 p-0 m-0 shadow'>";

                    foreach($url as $subItem => $subUrl)
                    {
                        if($subItem == $this->activeSubItem)
                        {
                            $menu .= 
                                    "<li><a href='$subUrl' class='dropdown-item active'>$subItem</a></li>";
                        }
                        else    
                        {
                            $menu .= 
                                    "<li><a href='$subUrl' class='dropdown-item'>$subItem</a></li>";
                        }
                    }
                        $menu .=
                                "</ul>
                            </li>";
                }
                else
                {
                    $menu .= 
                        "<li class='nav-item active'>
                            <a href='$url' class='nav-link'>$item</a>
                        </li>";
                }
            }
            else
            {
                if(is_array($url))
                {
                    $menu .= 
                        "<li class='nav-item dropdown'>
                            <a id='dropdownSubMenu' href='' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' class='nav-link dropdown-toggle'>$item</a>
                                <ul aria-labelledby='dropdownSubMenu' class='dropdown-menu border-0 p-0 m-0 shadow'>";

                    foreach($url as $subItem => $subUrl)
                    {
                        $menu .= 
                                "<li><a href='$subUrl' class='dropdown-item'>$subItem</a></li>";
                    }
                        $menu .=
                                "</ul>
                            </li>";
                }
                else
                {
                    $menu .= 
                        "<li class='nav-item'>
                            <a href='$url' class='nav-link'>$item</a>
                        </li>";
                }
            }
        }
        
        $menu .= "</ul>";
        
        return $menu;
    }
}