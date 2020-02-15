<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Authentication\AuthenticationService;
use Laminas\Db\Sql\Sql;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;

class AuthModel
{
    private $authService;
    private $config;
    private $db;
    private $administradorModel;
    
    public function __construct(
            AuthenticationService $authService,
            $config  = [],
            Db $db,
            AdministradorModel $administradorModel
    )
    {
        $this->authService        = $authService;
        $this->config             = $config;
        $this->db                 = $db->salab;
        $this->administradorModel = $administradorModel;
    }

    public function login($matricula, $senha)
    {
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setMatricula($matricula);
        $authAdapter->setSenha($senha);
        $result = $this->authService->authenticate();
        
        return $result;
    }

    public function forgotPassword($post)
    {
        $sql = new Sql($this->db);
        
        $matricula = $this->administradorModel->getMatricula($post['matricula']);
        
        if(empty($matricula))
        {
            throw new \Exception('Matrícula não pertence a nenhum usuário do sistema.');
        }
        
        $email = $this->administradorModel->getEmail($post['email']);
        
        if(empty($email))
        {
            throw new \Exception('Email não está cadastrado no sistema.');
        }
        
        $where = new \Laminas\Db\Sql\Where();
        $where->equalTo('matricula', $matricula['matricula'])
              ->AND
              ->equalTo('email', $email['email']);
        
        $select = $sql
            ->select('tb_usuarios')
            ->where($where);
        
        $result = $sql->prepareStatementForSqlObject($select)->execute()->current();
        
        if(empty($result))
        {
            throw new \Exception('Matrícula e Email não conferem.');
        }
        
        try 
        {
            $mail = new Message();
            $mail->addFrom('projetosalab2020@gmail.com', 'SALAB');
            $mail->addTo($email['email']);
            $mail->setSubject('foiii.');
            $mail->setBody('KKKKKK');
            $mail->setEncoding("UTF-8");

            $mail->getHeaders()->addHeaderLine('X-API-Key', 'FOO-BAR-BAZ-BAT');

            $options   = new SmtpOptions([
                
               // Setar aqui os dados vindo do config.
                
                //$this->config['smtp_accounts']['gmail']
            ]);
            
            $transport = new Smtp();
            $transport->setOptions($options);
            $transport->send($mail);
        } 
        catch (\Exception $ex) 
        {
            throw new \Exception('Erro ao enviar o email, tente novamente mais tarde.' . $ex->getMessage());
        }
    }
    
    public function logout()
    {
        if($this->authService->getIdentity() != null)
        {
            $this->authService->clearIdentity();
        }
    }
    
    public function filterAccess($controllerName, $actionName)
    {      
        $mode = isset($this->config['options']['mode']) ? $this->config['options']['mode'] : 'restrictive';
        
        if($mode != 'restrictive' && $mode != 'permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');

        if(isset($this->config['controllers'][$controllerName])) 
        {
            $items = $this->config['controllers'][$controllerName];
            
            foreach($items as $item) 
            {
                $actionList = isset($item['actions']) ? $item['actions'] : '*';
                $allow = $item['allow'];
                
                if(is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList == '*') 
                {
                    if($allow == '*')
                        return true; // Anyone is allowed to see the page.
                    else if($allow == '@' && $this->authService->hasIdentity()) 
                    {
                        return true; // Only authenticated user is allowed to see the page.
                    } else 
                    {                    
                        return false; // Access denied.
                    }
                }
            }            
        }

        // In restrictive mode, we forbid access for authenticated users to any 
        // action not listed under 'access_filter' key (for security reasons).
        if($mode == 'restrictive' && !$this->authService->hasIdentity())
            return false;

        // Permit access to this page.
        return true;
    }
}