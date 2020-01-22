<?php

namespace Application\Controller;

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

                    //$this->flashMessenger()->addSuccessMessage("Cadastrar Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador');
                }
                catch (\Exception $exc)
                {
                    //$this->flashMessenger()->addErrorMessage("Cadastrar Usuário| Ocorreu um erro durante a operação.");

                    return $this->redirect()->toRoute('administrador');
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
}
