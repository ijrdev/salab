<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\AuthController;
use Application\Model\AuthModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Validator\AbstractValidator;

class Module
{
    const VERSION = '3.1.3';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $event)
    {
//        $configs = $event->getApplication()->getServiceManager()->get('Config');
//        
//        if(isset($configs['force_https']) && $configs['force_https'])
//        {
//            $this->_initForceSSL();
//        }
        
        setlocale(LC_ALL, 'pt_BR');
        
        $translator = $event->getApplication()->getServiceManager()->get('MvcTranslator');
        $translator->addTranslationFile('phpArray', './vendor/laminas/resources/languages/pt_BR/Zend_Validate.php');
        $translator->addTranslationFile('phpArray', './vendor/laminas/resources/languages/pt_BR/Zend_Captcha.php');
        AbstractValidator::setDefaultTranslator($translator);
        
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method. 
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }
    
    public function onDispatch(MvcEvent $event)
    {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName     = $event->getRouteMatch()->getParam('action', null);
        
        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        
        // Get the instance of AuthManager service.
        $authModel = $event->getApplication()->getServiceManager()->get(AuthModel::class);
        
        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        if ($controllerName != AuthController::class && 
            !$authModel->filterAccess($controllerName, $actionName)) 
        {
            // Remember the URL of the page the user tried to access. We will
            // redirect the user to that URL after successful login.
            // $uri = $event->getApplication()->getRequest()->getUri();
            // Make the URL relative (remove scheme, user info, host name and port)
            // to avoid redirecting to other domain by a malicious user.
//            $uri->setScheme(null)
//                ->setHost(null)
//                ->setPort(null)
//                ->setUserInfo(null);
//            $redirectUrl = $uri->toString();
            
            // Redirect the user to the "Login" page.
            return $controller->redirect()->toRoute('login');
        }
        
//        if($controllerName != "Application\Controller\AuthController")
//        {
//            $auth    = new \Laminas\Authentication\AuthenticationService();
//            $usuario = $auth->getIdentity();
//            
//            if(isset($usuario) && !empty($usuario))
//            {
//                if($usuario['grupo'] != $controllerName)
//                {
//                    return $controller->redirect()->toRoute('login');
//                }
//            }
//        }
    }
}

