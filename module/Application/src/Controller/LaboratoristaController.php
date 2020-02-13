<?php

namespace Application\Controller;

use Application\Form\AlterarReservaForm;
use Application\Form\AvisoForm;
use Application\Form\OcuparLaboratorioForm;
use Application\Form\PerfilLaboratoristaForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Hashids\Hashids;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class LaboratoristaController extends AbstractActionController
{
    private $sessionModel;
    private $laboratoristaModel;
    private $administradorModel;
    
    public function __construct(SessionModel $sessionModel, LaboratoristaModel $laboratoristaModel, AdministradorModel $administradorModel) 
    {
        $this->sessionModel       = $sessionModel;
        $this->laboratoristaModel = $laboratoristaModel;
        $this->administradorModel = $administradorModel;
    }
    
    public function indexAction()
    {
        return new ViewModel([
            'avisos' => $this->administradorModel->getAllAvisos()
        ]);
    }
    
    public function mostrarImagemAction()
    {
        $hash = $this->params()->fromRoute('id', 0);
        
        $decodeHash = new Hashids('id', 10);
        $decode     = $decodeHash->decode($hash);
        
        if(empty($decode))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
        
        $anexo = $this->administradorModel->getAnexo($decode[0]);
        
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
    
    public function laboratoriosAction()
    {
        $page   = $this->params()->fromQuery('page', 1);
        $search = $this->params()->fromQuery('search', null);
        
        try
        {
            $laboratorios = $this->laboratoristaModel->getAllLabors($page, $search);
        } 
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addErrorMessage('Laboratórios| Ocorreu um problema ao realizar a operação.');

            return $this->redirect()->toRoute('laboratorista');
        }

        return new ViewModel([
            'laboratorios' => $laboratorios,
            'search'       => $search
        ]);
    }
    
    public function ocuparLaboratorioAction()
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
  
        $form = new OcuparLaboratorioForm($this->laboratoristaModel);

        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->ocuparLaboratorio($form->getData(), $this->sessionModel->getUsuario()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Ocupar Laboratório| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'laboratorios']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Ocupar Laboratório| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'laboratorios']);
                }
            }
            else
            {
                $form->setData($post);
            }
        }
        else
        {
            $lab                = explode(' ', $laboratorio['lab']);
            $laboratorio['lab'] = $lab[1];
            
            $form->setData($laboratorio);
        }

        return new ViewModel([
            'form'        => $form,
            'laboratorio' => $laboratorio
        ]);
    }
    
    public function getReservaAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost() && $request->isXmlHttpRequest())
        {
            $post = $request->getPost();

            try 
            {
                $reserva = $this->laboratoristaModel->getLaboratorioReservas($post['id_laboratorio'], $post['dt_reserva']);
            }
            catch (\Exception $exc)
            {
                $reserva = $exc->getMessage();
            }
            
            return new JsonModel([
                'reserva' => $reserva
            ]);
        }
    }
    
    public function reservasAction()
    {
        $page   = $this->params()->fromQuery('page', 1);
        $search = $this->params()->fromQuery('data', null);
        
        try
        {
            $laboratorios = $this->laboratoristaModel->getAllLabors($page, $search, 'param');
        } 
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addErrorMessage('Laboratórios| Ocorreu um problema ao realizar a operação.');

            return $this->redirect()->toRoute('laboratorista');
        }

        return new ViewModel([
            'laboratorios' => $laboratorios,
            'search'       => $search
        ]);
    }
    
    public function alterarReservaAction()
    {
        $id_reserva = (int) $this->params()->fromRoute('id', 0);
        
        $reserva = $this->laboratoristaModel->getReserva($id_reserva);
  
        if(empty($reserva))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }

        $form = new AlterarReservaForm();
        
        $request = $this->getRequest();

        if($request->isPost()) 
        {
            $post = $this->params()->fromPost();

            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {      
                    $this->laboratoristaModel->alterarReserva($form->getData(), $reserva['id_reserva'], $reserva['horario'], $this->sessionModel->getUsuario()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Alterar Reserva| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'reservas']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Alterar Reserva| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'reservas']);
                }
            }
            else
            {
                $form->setData($post);
            }
        }
        else
        {
            $lab            = explode(' ', $reserva['lab']);
            $reserva['lab'] = $lab[1];
                        
            $form->setData($reserva);
        }
                
        return new ViewModel([
            'form'    => $form,
            'reserva' => $reserva,
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

                    return $this->redirect()->toRoute('laboratorista');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Aviso| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('laboratorista');
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
    
    public function perfilAction()
    {
        $usuario = $this->sessionModel->getUsuario();
        
        if(!isset($usuario) || empty($usuario))
        {
            $this->getResponse()->setStatusCode(404);
            
            return;
        }
  
        $form = new PerfilLaboratoristaForm($this->administradorModel);
        
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
                    $this->administradorModel->perfil($form->getData(), $usuario['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Perfil| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'perfil']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Perfil| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('laboratorista', ['action' => 'perfil']);
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
}
