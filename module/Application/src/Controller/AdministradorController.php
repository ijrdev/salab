<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdministradorController extends AbstractActionController
{
    public function __construct() {
        ;
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }
}
