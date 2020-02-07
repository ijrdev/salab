<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\AlterarReservaForm;
use Application\Form\AvisoForm;
use Application\Form\PerfilLaboratoristaForm;
use Application\Model\AdministradorModel;
use Application\Model\LaboratoristaModel;
use Application\Model\SessionModel;
use Laminas\Mvc\Controller\AbstractActionController;
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
                    $this->laboratoristaModel->alterarReserva($form->getData(), $reserva['id_reserva'], $reserva['horario']);

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
                    $this->administradorModel->updatePerfil($form->getData(), $usuario['id_usuario']);

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
