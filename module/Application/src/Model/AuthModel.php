<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
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
        
        $whereSelect = new Where();
        $whereSelect->equalTo('matricula', $matricula['matricula'])
              ->AND
              ->equalTo('email', $email['email']);
        
        $select = $sql
            ->select('tb_usuarios')
            ->where($whereSelect);
        
        $result = $sql->prepareStatementForSqlObject($select)->execute()->current();
        
        if(empty($result))
        {
            throw new \Exception('Matrícula e Email não conferem.');
        }
        
        try 
        {
            $nova_senha = mt_rand(100000, 999999);
            
            $bcrypt      = new Bcrypt();
            $newPassword = $bcrypt->create($nova_senha);

            $whereUpdate = new Where();
            $whereUpdate->equalTo('matricula', $matricula['matricula'])
                        ->AND
                        ->equalTo('email', $email['email']);

            $update = $sql
                ->update('tb_usuarios')
                ->set([
                    'senha' => $newPassword,
                ])
                ->where($whereUpdate);

            $sql->prepareStatementForSqlObject($update)->execute();
            
            // Responsável por juntar o corpo do email com arquivos/imagens/anexos...
//            $mimePart       = new Part($this->templateEmail($nova_senha));
//            $mimePart->type = 'text/html';
                
//            $mimePartAnexo       = new Part(fopen($file, 'r'));
//            $mimePartAnexo->type = self::getContentType($file);
//            $mimePartAnexo->filename = end($filename);
//            $mimePartAnexo->disposition = (\Laminas\Mime\Mime::DISPOSITION_ATTACHMENT);
            
            // Responsável por pegar o corpo da mensagem e juntar com anexos, caso contenha no email.
//            $mimeMessage = new Message();
//            $mimeMessage->setParts($mimePart);
            
            // Configuração para enviar email via Gmail.
            // OBS: Habilitar a permissão de acesso/envio de email no Gmail.
            $mail = new Message;
            $mail->addFrom('projetosalab2020@gmail.com', 'SALAB');
            $mail->addTo($email['email']);
            $mail->setSubject("Recuperação de senha.");
            $mail->setBody($this->templateEmail($nova_senha));
            $mail->setEncoding("UTF-8");

            $mail->getHeaders()->addHeaderLine('Content-Type', 'text/html');

            $options = new SmtpOptions([
                'host' => $this->config['smtp_accounts']['gmail']['host'],
                'port' => $this->config['smtp_accounts']['gmail']['port'],
                'connection_class'  => $this->config['smtp_accounts']['gmail']['connection_class'],
                'connection_config' => [
                    'username' => $this->config['smtp_accounts']['gmail']['connection_config']['username'],
                    'password' => $this->config['smtp_accounts']['gmail']['connection_config']['password'],
                    'ssl'      => $this->config['smtp_accounts']['gmail']['connection_config']['ssl']
                ]
            ]);
            
            // Responsável por receber/setar as configurações e enviar o email.
            $transport = new Smtp();
            $transport->setOptions($options);
            $transport->send($mail);
        } 
        catch (\Exception $ex) 
        {
            throw new \Exception('Erro ao enviar o email, tente novamente mais tarde.' . $ex->getMessage());
        }
    }
    
    public function templateEmail($nova_senha)
    {
        $template = "<html>
                        <div>
                            <h3>SALAB - Sistema de Agendamento de Laboratórios</h3>

                            <p>Sua nova senha é: $nova_senha.</p>

                            <p> <strong>OBS: </strong> Para alterá-la acesse seu perfil no menu após realizar o acesso. </p>
                        </div>
                    </html>";

        return $template;
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