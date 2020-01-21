<?php

namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Model\AuthModel;
use Application\Model\SessionModel;
use Laminas\Authentication\Result;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    private $authModel;
    private $sessionModel;
    
    public function __construct(AuthModel $authModel, SessionModel $sessionModel)
    {
        $this->authModel    = $authModel;
        $this->sessionModel = $sessionModel;
    }
    
    public function loginAction()
    {
        $this->layout('layout/login');
        
//        $auth = new \Laminas\Authentication\AuthenticationService();
//        $auth->clearIdentity();
        
        $form = new LoginForm();
        
        if($this->getRequest()->isPost())
        {
            $post = $this->params()->fromPost();

            $form->setData($post);
            
            if($form->isValid())
            {
                try
                {    
                    $result = $this->authModel->login($form->getData()['matricula'], $form->getData()['senha']);
                    
                    if($result->getCode() == Result::SUCCESS)
                    {
                        switch($result->getIdentity()['id_grupo'])
                        {
                            case 1:
                                return $this->redirect()->toRoute('administrador');
                                break;
                            case 2:
                                return $this->redirect()->toRoute('laboratorista');
                                break;
                            case 3:
                                return $this->redirect()->toRoute('professor');
                                break;
                        }
                    }
                }
                catch (\Exception $exc)
                {
//                    $this->flashMessenger()->addErrorMessage("Editar Chamado| Ocorreu um erro na edição do chamado.");

                    return $this->redirect()->toRoute('login');
                }
            }
        }
        
        return new ViewModel([
            'form' => $form
        ]);
    }
}
