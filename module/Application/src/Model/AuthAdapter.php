<?php

namespace Application\Model;

use Application\Adapter\Db;
use Application\Model\SessionModel;
use Application\Model\AdministradorModel;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Sql;

class AuthAdapter implements AdapterInterface
{
    private $db;
    private $sessionModel;
    private $administradornModel;
    private $matricula;
    private $senha;
    
    public function __construct(Db $db, SessionModel $sessionModel, AdministradorModel $administradornModel)
    {
        $this->db                  = $db->salab;
        $this->sessionModel        = $sessionModel;
        $this->administradornModel = $administradornModel;
    }
    
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    public function setSenha($senha)
    {
        $this->senha = (string) $senha;
    }

    public function authenticate()
    {
        $matricula = $this->matricula;
        $senha     = $this->senha;
        
        $sql = new Sql($this->db);
        
        $select = $sql
            ->select(['u' => 'tb_usuarios'])
            ->join(['g'   => 'tb_grupos'], 'u.id_grupo = g.id_grupo', 'grupo')
            ->where(['u.matricula' => $matricula]);
        
        $result = $sql->prepareStatementForSqlObject($select)->execute()->current();
        
        if(!empty($result))
        {
            $bcrypt     = new Bcrypt();
            $securePass = $result['senha'];

            if($bcrypt->verify($senha, $securePass))
            {
                $update = $sql
                    ->update('tb_usuarios')
                    ->set(['dthr_ult_acesso' => date('Y-m-d H:i:s')])
                    ->where(['matricula'     => $matricula]);

                $sql->prepareStatementForSqlObject($update)->execute();
                
                $this->sessionModel->setUsuario($result);

                return new Result(Result::SUCCESS, $this->sessionModel->getUsuario(), ['Login realizado com sucesso!']);
            } 
            else
            {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Senha incorreta.']);
            }
        }
        else
        {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, ['Matrícula não está cadastrada no sistema.']);
        }
    }
}