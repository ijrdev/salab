<?php

namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Model\AuthModel;
use Application\Model\SalabModel;
use Application\Model\SessionModel;
use Laminas\Authentication\Result;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    private $authModel;
    private $sessionModel;
    private $salabModel;
    
    public function __construct(AuthModel $authModel, SessionModel $sessionModel, SalabModel $salabModel)
    {
        $this->authModel    = $authModel;
        $this->sessionModel = $sessionModel;
        $this->salabModel = $salabModel;
    }
    
    public function loginAction()
    {
        $this->layout('layout/login');
        
//        $auth = new \Laminas\Authentication\AuthenticationService();
//        $auth->clearIdentity();
        
//        $this->salabModel->addUser([
//            'matricula' => 20191,
//            'email' => 'adm@gmail.com',
//            'senha' => 123456,
//            'grupo' => 1
//        ]);
        
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
                                $this->flashMessenger()->addSuccessMessage("Acesso| " . $result->getMessages()[0]);
                                
                                return $this->redirect()->toRoute('administrador');
                                break;
                            case 2:
                                $this->flashMessenger()->addSuccessMessage("Acesso| " . $result->getMessages()[0]);
                                
                                return $this->redirect()->toRoute('laboratorista');
                                break;
                            case 3:
                                $this->flashMessenger()->addSuccessMessage("Acesso| " . $result->getMessages()[0]);
                                
                                return $this->redirect()->toRoute('professor');
                                break;
                        }
                    }
                    else if($result->getCode() == Result::FAILURE_CREDENTIAL_INVALID)
                    {
                        $this->flashMessenger()->addErrorMessage("Acesso| " . $result->getMessages()[0]);
                    }
                    else if($result->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND)
                    {
                        $this->flashMessenger()->addErrorMessage("Acesso| " . $result->getMessages()[0]);
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
