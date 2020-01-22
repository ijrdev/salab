<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Messenger extends AbstractHelper
{
    private $success = '';
    private $error   = '';
    private $warning = '';
    private $info    = '';
    
    public function addSuccessMessage($message = '') 
    {
        $this->success = $message;
    }

    public function addErrorMessage($message = '') 
    {
        $this->error = $message;
    }
    
    public function addWarningMessage($message = '') 
    {
        $this->warning = $message;
    }

    public function addInfoMessage($message = '') 
    {
        $this->info = $message;
    }
        
    public function render()
    {
        if(!empty($this->success) && $this->success != '')
        {
            return $messeger = 
                "<div id='toast-container' class='toast-top-right'>
                    <div class='toast toast-success' aria-live='polite' style='opacity: 0.188862;'>
                        <div class='toast-message'> $this->success </div>
                    </div>
                </div>";
        }
        
        if(!empty($this->error) && $this->error != '')
        {
            return $messeger = 
                "<div id='toast-container' class='toast-top-right'>
                    <div class='toast toast-error' aria-live='polite' style='opacity: 0.188862;'>
                        <div class='toast-message'> $this->error </div>
                    </div>
                </div>";
        }
        
        if(!empty($this->warning) && $this->warning != '')
        {
            return $messeger = 
                "<div id='toast-container' class='toast-top-right'>
                    <div class='toast toast-warning' aria-live='polite' style='opacity: 0.188862;'>
                        <div class='toast-message'> $this->warning </div>
                    </div>
                </div>";
        }
        
        if(!empty($this->info) && $this->info != '')
        {
            return $messeger = 
                "<div id='toast-container' class='toast-top-right'>
                    <div class='toast toast-info' aria-live='polite' style='opacity: 0.188862;'>
                        <div class='toast-message'> $this->info </div>
                    </div>
                </div>";
        }
    }
}