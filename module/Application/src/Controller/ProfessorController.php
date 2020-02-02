<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\AgendarForm;
use Application\Form\AvisoForm;
use Application\Form\PerfilProfessorForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class ProfessorController extends AbstractActionController
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
    
    public function minhasReservasAction()
    {
        return new ViewModel([

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
                $reserva = $this->laboratoristaModel->getReserva($post['id_laboratorio'], $post['dt_reserva']);
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
    
    public function checkReservaAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost() && $request->isXmlHttpRequest())
        {
            $post = $request->getPost();

            try 
            {
                $checkReserva = $this->laboratoristaModel->checkReserva($post['id_laboratorio'], $post['dt_reserva'], $post['horario']);
            }
            catch (\Exception $exc)
            {
                $checkReserva = $exc->getMessage();
            }
            
            return new JsonModel([
                'checkReserva' => $checkReserva
            ]);
        }
    }
    
    VERIFICAR O PROCEDIMENTO DE RESERVAR E AGENDAR
    POIS A RESERVA ESTÁ VINCULADA AO LABORATÓRIO E O
    AGENDAMENTO SE DÁ APÓS A RESERVA
    
    
    CRIAR O AGENDAMENTO DO USUÁRIO APÓS RESERVAR O LABORATÓRIO(NOVA TABELA DE VÍNCULO).
    
    
    
    
    
    
    
    
    public function agendarAction()
    {
        $form = new AgendarForm($this->laboratoristaModel);
        
        if($this->getRequest()->isPost()) 
        {
            $post = $this->params()->fromPost();
            
            $form->setData($post);

            if($form->isValid()) 
            {
                try 
                {
                    $this->laboratoristaModel->agendar($form->getData(), $this->sessionModel->getUsuario()['id_usuario']);

                    $this->flashMessenger()->addSuccessMessage("Agendar| Operação realizada com sucesso!");

                    return $this->redirect()->toRoute('professor');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Agendar| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('professor');
                }
            }
        }

        return new ViewModel([
            'form' => $form
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

                    return $this->redirect()->toRoute('professor');
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Aviso| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('professor');
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
  
        $form = new PerfilProfessorForm($this->administradorModel);
        
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

                    return $this->redirect()->toRoute('professor', ['action' => 'perfil']);
                }
                catch (\Exception $exc)
                {
                    $this->flashMessenger()->addErrorMessage('Perfil| Ocorreu um problema ao realizar a operação.');

                    return $this->redirect()->toRoute('professor', ['action' => 'perfil']);
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
