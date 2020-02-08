<?php

namespace Application\Controller;

use Application\Form\AlterarLaboratorioForm;
use Application\Form\AlterarUsuarioForm;
use Application\Form\AvisoForm;
use Application\Form\CadastrarLaboratorioForm;
use Application\Form\CadastrarUsuarioForm;
use Application\Form\ExcluirLaboratorioForm;
use Application\Form\ExcluirUsuarioForm;
use Application\Form\PerfilAdministradorForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdministradorController extends AbstractActionController
{
    private $sessionModel;
    private $administradorModel;
    private $laboratoristaModel;
    
    public function __construct(SessionModel $sessionModel, AdministradorModel $administradorModel, LaboratoristaModel $laboratoristaModel)
    {
        $this->sessionModel = $sessionModel;
        $this->administradorModel = $administradorModel;
        $this->laboratoristaModel = $laboratoristaModel;
    }
    
    public function indexAction()
    {
        return new ViewModel([
            'avisos'       => $this->administradorModel->getAllAvisos(),
            'agendamentos' => $this->laboratoristaModel->getCountAllAgendamentos(),
            'countAvisos'  => $this->administradorModel->getCountAllAvisos(),
            'usuarios'     => $this->administradorModel->getCountAllUsers(),
            'laboratorios' => $this->laboratoristaModel->getCountAllLabors(),
        ]);
    }
    
    public function mostrarImagemAction()
    {
        $id_anexo = (int) $this->params()->fromRoute('id', 0);
        
        if(!$id_anexo)
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
        
        $anexo = $this->administradorModel->getAnexo($id_anexo);
        
        if(empty($anexo))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
        
        $viewModel = new ViewModel($anexo);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('templates/image');
        
        return $viewModel;
    }
    
    public function agendamentosAction()
    {
        $page   = $this->params()->fromQuery('page', 1);
        $search = $this->params()->fromQuery('search', null);
        $data   = $this->params()->fromQuery('data', null);
        
        try
        {
            $agendamentos = $this->administradorModel->getAgendamentos($page, $search, $data);
        } 
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addErrorMessage('Agendamentos| Ocorreu um problema ao realizar a operação.');

            return $this->redirect()->toRoute('administrador');
        }

        return new ViewModel([
            'agendamentos' => $agendamentos,
            'search'       => $search,
            'data'         => $data
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
                    $this->administradorModel->add($form->getData(), $this->sessionModel->getUsuario()['id_usuario']);

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
                    $this->laboratoristaModel->add($form->getData(), $this->sessionModel->getUsuario()['id_usuario']);

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
            $usuarios = $this->administradorModel->getAllUsers($page, $search, $this->sessionModel->getUsuario()['id_usuario']);
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
    
    public function alterarUsuarioAction()
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
  
        $form = new AlterarUsuarioForm($this->administradorModel);

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->administradorModel->update($form->getData(), $id_usuario, $this->sessionModel->getUsuario()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Alterar Usuário| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-usuarios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Alterar Usuário| Ocorreu um problema ao realizar a operação.');

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
    
    public function alterarLaboratorioAction()
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
  
        $form = new AlterarLaboratorioForm();

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->update($form->getData(), $id_laboratorio, $this->sessionModel->getUsuario()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Alterar Laboratório| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('administrador', ['action' => 'consultar-laboratorios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Alterar Laboratório| Ocorreu um problema ao realizar a operação.');

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
                    $this->administradorModel->delete($id_usuario, $this->sessionModel->getUsuario()['id_usuario']);

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
                    $this->laboratoristaModel->delete($id_laboratorio, $this->sessionModel->getUsuario()['id_usuario']);

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
        
        return new ViewModel([
            'form'        => $form,
            'laboratorio' => $laboratorio
        ]);
    }
    
    public function perfilAction()
    {
        $usuario = $this->sessionModel->getUsuario();
        
        if(!isset($usuario) || empty($usuario))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
  
        $form = new PerfilAdministradorForm($this->administradorModel);
        
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
                $form->setData($values);
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
                    $this->administradorModel->aviso($form->getData(), $this->sessionModel->getUsuario()['id_usuario']);

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
    
    public function reservaAction()
    {
        return new ViewModel([
            
        ]);
    }
}
