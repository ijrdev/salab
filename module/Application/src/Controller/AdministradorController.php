<?php

namespace Application\Controller;

use Application\Form\CadastrarLaboratorioForm;
use Application\Form\CadastrarUsuarioForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdministradorController extends AbstractActionController
{
    private $salabModel;
    
    public function __construct(\Application\Model\SalabModel $salabModel) {
        $this->salabModel = $salabModel;
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function cadastrarUsuarioAction()
    {
        $form = new CadastrarUsuarioForm($this->salabModel);
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->salabModel->addUser($form->getData());

                    $this->flashMessenger()->addSuccessMessage("Cadastrar Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage($exc->getMessage());

                    return $this->redirect()->toRoute('administrador');
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
    
    public function cadastrarLaboratorioAction()
    {
        $form = new CadastrarLaboratorioForm($this->salabModel);
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->salabModel->addLab($form->getData());

                    $this->flashMessenger()->addSuccessMessage("Cadastrar Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage($exc->getMessage());

                    return $this->redirect()->toRoute('administrador');
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
}
