<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Menu extends AbstractHelper
{
    private $items      = [];
    private $activeItem = '';

    public function setItems($items) 
    {
        $this->items = $items;
    }
    
    public function setActiveItem($activeItem) 
    {
        $this->activeItem = $activeItem;
    }
    
    public function render()
    {
        if(empty($this->items))
        {
            return '';
        }
        
        $menu = "<ul class='navbar-nav'>";
        
        foreach($this->items as $item => $url) 
        {
            if(is_array($url))
            {
                $menu .= 
                    "<li class='nav-item dropdown'>
                        <a id='dropdownSubMenu' href='' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' class='nav-link dropdown-toggle'>$item</a>
                            <ul aria-labelledby='dropdownSubMenu' class='dropdown-menu border-0 shadow'>";
                                
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
        
        $menu .= "</ul>";
        
        return $menu;
    }
}