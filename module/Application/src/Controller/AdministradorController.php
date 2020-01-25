<?php

namespace Application\Controller;

use Application\Form\CadastrarLaboratorioForm;
use Application\Form\CadastrarUsuarioForm;
use Application\Form\EditarLaboratorioForm;
use Application\Form\EditarUsuarioForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdministradorController extends AbstractActionController
{
    private $administradorModel;
    private $laboratoristaModel;
    
    public function __construct(AdministradorModel $administradorModel, LaboratoristaModel $laboratoristaModel)
    {
        $this->administradorModel = $administradorModel;
        $this->laboratoristaModel = $laboratoristaModel;
    }
    
    public function indexAction()
    {
        return new ViewModel([
            'usuarios'     => $this->administradorModel->getCountAllUsers(),
            'laboratorios' => $this->laboratoristaModel->getCountAlLabors(),
        ]);
    }
    
    public function perfilAction()
    {
        

        return new ViewModel([
            
        ]);
    }
    
    public function cadastrarUsuarioAction()
    {
        $form = new CadastrarUsuarioForm($this->administradorModel);
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->add($form->getData());

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
        $form = new CadastrarLaboratorioForm();
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->add($form->getData());

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
    
    public function consultarUsuariosAction()
    {
        $page   = $this->params()->fromQuery('page', 1);
        $search = $this->params()->fromQuery('search', null);
        
        try
        {
            $usuarios = $this->administradorModel->getAllUsers($page, $search);
        } 
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addErrorMessage('Consultar Usuários| Ocorreu um problema ao realizar a operação.');

            return $this->redirect()->toRoute('administrador');
        }

        return new ViewModel([
            'usuarios' => $usuarios,
            'search'   => $search
        ]);
    }
    
    public function consultarLaboratoriosAction()
    {
        $page   = $this->params()->fromQuery('page', 1);
        $search = $this->params()->fromQuery('search', null);
        
        try
        {
            $laboratorios = $this->laboratoristaModel->getAllLabors($page, $search);
        } 
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addErrorMessage('Consultar Laboratórios| Ocorreu um problema ao realizar a operação.');

            return $this->redirect()->toRoute('administrador');
        }

        return new ViewModel([
            'laboratorios' => $laboratorios,
            'search'       => $search
        ]);
    }
    
    public function editarUsuarioAction()
    {
        $id_usuario = (int) $this->params()->fromRoute('id', 0);
        
        if(!$id_usuario)
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
        
        $usuario = $this->administradorModel->getUser($id_usuario);
        
        if(!isset($usuario) || empty($usuario))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
  
        $form = new EditarUsuarioForm($this->administradorModel);

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->update($form->getData(), $id_usuario);

                    $this->flashMessenger()->addSuccessMessage("Editar Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-usuarios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Editar Usuário| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-usuarios']);
                }
            }
            else
            {
                $form->setData($post);
            }
        }
        else
        {
            $usuario['grupo'] = $usuario['id_grupo'];
            
            $form->setData($usuario);
        }
        
        return new ViewModel([
            'form'    => $form,
            'usuario' => $usuario
        ]);
    }
    
    public function editarLaboratorioAction()
    {
        $id_laboratorio = (int) $this->params()->fromRoute('id', 0);
        
        if(!$id_laboratorio)
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
        
        $laboratorio = $this->laboratoristaModel->getLabor($id_laboratorio);
        
        if(!isset($laboratorio) || empty($laboratorio))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
  
        $form = new EditarLaboratorioForm();

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->update($form->getData(), $id_laboratorio);

                    $this->flashMessenger()->addSuccessMessage("Editar Laboratório| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-laboratorios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Editar Laboratório| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-laboratorios']);
                }
            }
            else
            {
                $form->setData($post);
            }
        }
        else
        {
            $lab = explode(' ', $laboratorio['lab']);
            $laboratorio['lab'] = $lab[1];
            
            $form->setData($laboratorio);
        }

        return new ViewModel([
            'form'        => $form,
            'laboratorio' => $laboratorio
        ]);
    }
}
