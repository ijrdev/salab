<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;

class UsuarioModel
{
    private $db;

    public function __construct(Db $db)
    {
       $this->db = $db->salab;
    }
    
    public function add($post)
    {
        $sql = new Sql($this->db);
        
        $bcrypt      = new Bcrypt();
        $newPassword = $bcrypt->create($post['senha']);        
        
        $insert = $sql
            ->insert('tb_usuarios')
            ->values([
                'matricula' => $post['matricula'],
                'nome'      => $post['nome'],
                'sobrenome' => $post['sobrenome'],
                'email'     => $post['email'],
                'senha'     => $newPassword,
            ]);
        
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
    
    public function fetchEmail($email)
    {
        $where = new Where();
        $where->equalTo('email', $email);
        
        $select = $this->db
            ->select('tb_usuarios')
            ->where($where);
        
        return $this->db->prepareStatementForSqlObject($select)->execute();
    }
}

