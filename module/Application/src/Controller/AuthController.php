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
//        EMAIL.
//        
//        VER UM LOGO LEGAL DA APLICAÇÃO E O ÍCONE DA ABA.
//   
//        TESTES EM DISPOSITIVO MÓVEL.
//                  
//        ÚLTIMO TESTE COMPLETO.
//        
//        ALGUNS AJUSTES DE CONFIGURAÇÕES DO PROJETO.
//        VERIFICAR ESSA QUESTAO DOS MODOS DE PRODUCAO E DESENVOLVIMENTO. (IGOR/MARCOS/IVES)
//        
//        AJUSTAR OS ERROS, MOSTRA APENAS O ERRO E NÃO O MOTIVO. (config/autoload/development.local.php)
        
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
                    $this->flashMessenger()->addErrorMessage("Acesso| Ocorreu um erro ao realizar o acesso.");

                    return $this->redirect()->toRoute('auth');
                }
            }
        }
        
        return new ViewModel([
            'form' => $form
        ]);
    }
    
    public function recuperarSenhaAction()
    {
        $this->layout('layout/recuperar-senha');
        
        $form = new \Application\Form\RecuperarSenhaForm();
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->authModel->forgotPassword($form->getData());

                    $this->flashMessenger()->addSuccessMessage('Recuperar Senha| Operação realizada com sucesso!');

                    return $this->redirect()->toRoute('auth', ['action' => 'recuperar-senha']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Recuperar Senha| ' . $exc->getMessage());

                    return $this->redirect()->toRoute('auth', ['action' => 'recuperar-senha']);
                }
            }
            else
            {
                $form->setData($post);
            }
        }
        
        return new ViewModel([
            'form' => $form
        ]);
    }
    
    public function logoutAction()
    {
        $this->authModel->logout();
        
        return $this->redirect()->toRoute('auth');
    }
}
