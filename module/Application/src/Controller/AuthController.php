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
        
        $form = new LoginForm();
        
        if($this->getRequest()->isPost())
        {
            $post = $this->params()->fromPost();

            $form->setData($post);
            
            if($form->isValid())
            {
                try
                {    
                    // $auth = new \Laminas\Authentication\AuthenticationService();
                    // var_dump($auth->getIdentity());
                    
                    $result = $this->authModel->login($form->getData()['matricula'], $form->getData()['senha']);
                    
                    echo "<pre>";
                    print_r($result->getMessages());
                    exit;
                    
                    if($result->getCode() == Result::SUCCESS)
                    {
                        
                    }
                    
//                    $this->chamadosManager->edit($form->getData(), $id_chamado);
//
//                    $this->flashMessenger()->addSuccessMessage("Editar Chamado| Operação realizada com sucesso!");
//
//                    return $this->redirect()->toRoute('inicio-projetos');
                }
                catch (\Exception $exc)
                {
//                    $this->flashMessenger()->addErrorMessage("Editar Chamado| Ocorreu um erro na edição do chamado.");
//
//                    return $this->redirect()->toRoute('inicio-projetos');
                }
            }
        }
        
        return new ViewModel([
            'form' => $form
        ]);
    }
}
