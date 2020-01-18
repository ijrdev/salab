<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class PageTitle extends AbstractHelper
{
    private $title = '';
    private $subTitle = '';

    public function setTitle($title) 
    {
        $this->title = $title;
    }

    public function setSubTitle($subTitle) 
    {
        $this->subTitle = $subTitle;
    }
    
    public function render()
    {
        if(empty($this->title))
        {
            return '';
        }
        
        if(!empty($this->subTitle))
        {
            return $pageTitle = 
                "<div class='col-sm-6'>
                    <h1 class='m-0 text-dark'> $this->title </h1> <small> $this->subTitle </small>
                </div>";
        }
        
        return $pageTitle = 
                "<div class='col-sm-6'>
                    <h1 class='m-0 text-dark'> $this->title </h1>
                </div>";
    }
}