<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Breadcrumbs extends AbstractHelper
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
        else
        {
            $breadcrumb = 
                "<div class='col-sm-6'>
                    <ol class='breadcrumb float-sm-right' style='font-size: 14px;'>";

            foreach($this->items as $item => $url)
            {
                if($item == $this->activeItem)
                {
                    $breadcrumb .= "<li class='breadcrumb-item active'>$item</li>";
                }
                else
                {
                    $breadcrumb .= "<li class='breadcrumb-item'><a style='color: black;' href='$url'>$item</a></li>";
                }
            }

            $breadcrumb .= 
                "   </ol>
                </div>";

            return $breadcrumb;
        }
    }
}