<?php

namespace Application\Controller;

use Application\Form\AvisoForm;
use Application\Form\CadastrarLaboratorioForm;
use Application\Form\CadastrarUsuarioForm;
use Application\Form\EditarLaboratorioForm;
use Application\Form\EditarUsuarioForm;
use Application\Form\ExcluirLaboratorioForm;
use Application\Form\ExcluirUsuarioForm;
use Application\Form\PerfilForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdministradorController extends AbstractActionController
{
    private $authService;
    private $administradorModel;
    private $laboratoristaModel;
    
    public function __construct(AuthenticationService $authService, AdministradorModel $administradorModel, LaboratoristaModel $laboratoristaModel)
    {
        $this->authService = $authService;
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
        
        $usuario = $this->authService->getIdentity();
        
        try
        {
            $usuarios = $this->administradorModel->getAllUsers($page, $search, $usuario['id_usuario']);
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
    
    public function excluirUsuarioAction()
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
        
        $form = new ExcluirUsuarioForm();
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->delete($form->getData());

                    $this->flashMessenger()->addSuccessMessage("Excluir Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-usuarios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Excluir Usuário| Ocorreu um problema ao realizar a operação. ' . $exc->getMessage());

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-usuarios']);
                }
            }
        }
        else
        {
            $form->setData(['id_usuario' => $id_usuario]);
        }
        
        return new ViewModel([
            'form'    => $form,
            'usuario' => $usuario
        ]);
    }
    
    public function excluirLaboratorioAction()
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
        
        $form = new ExcluirLaboratorioForm();
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->delete($form->getData());

                    $this->flashMessenger()->addSuccessMessage("Excluir Laboratório| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-laboratorios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Excluir Laboratório| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-laboratorios']);
                }
            }
        }
        else
        {
            $form->setData(['id_laboratorio' => $id_laboratorio]);
        }
        
        return new ViewModel([
            'form'        => $form,
            'laboratorio' => $laboratorio
        ]);
    }
    
    public function perfilAction()
    {
        $usuario = $this->authService->getIdentity();
        
        if(!isset($usuario) || empty($usuario))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
  
        $form = new PerfilForm($this->administradorModel);

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->updatePerfil($form->getData(), $usuario['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Perfil| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'perfil']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Perfil| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('administrador', ['action' => 'perfil']);
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
            'usuario' => $usuario,
            'form'    => $form
        ]);
    }
    
    public function avisoAction()
    {
        $form = new AvisoForm();

        $request = $this->getRequest();
        
        if($request->isPost()) 
        {
            $values = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($values);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->aviso($form->getData(), $this->authService->getIdentity()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Aviso| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Aviso| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('administrador');
                }
            }
            else
            {
                $form->setData($values);
            }
        }
        
        return new ViewModel([
            'form' => $form
        ]);
    }
}
