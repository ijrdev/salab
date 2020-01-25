<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

class AdministradorModel
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
                'email'     => $post['email'],
                'senha'     => $newPassword,
                'id_grupo'  => $post['grupo'],
                'dthr_cad'  => date('Y-m-d H:i:s')
            ]);
        
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
    
    public function getAllUsers($page, $search)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        
        if(isset($search) && !empty($search))
        {
            $where->equalTo('id_usuario', strip_tags(trim($search)))
              ->OR
              ->like('matricula', '%' . strip_tags(trim($search)) . '%')
              ->OR
              ->like('email', '%' . strip_tags(trim($search)) . '%');
        }
        
        $select = $sql
            ->select('tb_usuarios')
            ->columns([
                'id_usuario',
                'matricula',
                'email',
                'id_grupo',
                'dthr_cad',
                'dthr_ult_alteracao'
            ])
            ->where($where);
        
        $pag_adapter = new DbSelect($select, $sql);
        $paginator   = new Paginator($pag_adapter);

        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        return $paginator;
    }
    
    public function getUser($id_usuario)
    {
        $sql = new Sql($this->db);

        $select = $sql
            ->select('tb_usuarios')
            ->columns([
                'id_usuario',
                'matricula',
                'email',
                'id_grupo',
                'dthr_cad',
                'dthr_ult_alteracao'
            ])
            ->where(['id_usuario' => $id_usuario]);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function getMatricula($matricula)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('matricula', $matricula);
        
        $select = $sql
            ->select('tb_usuarios')
            ->columns(['matricula'])
            ->where($where);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function getEmail($email)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('email', $email);
        
        $select = $sql
            ->select('tb_usuarios')
            ->columns(['email'])
            ->where($where);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function update($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $bcrypt      = new Bcrypt();
        $newPassword = $bcrypt->create($post['nova-senha']); 

        $update = $sql
            ->update('tb_usuarios')
            ->set([
                'matricula'          => $post['matricula'],
                'email'              => $post['email'],
                'senha'              => $newPassword,
                'id_grupo'           => $post['grupo'],
                'dthr_ult_alteracao' => date('Y-m-d H:i:s')
            ])
            ->where(['id_usuario' => $id_usuario]);
        
        $sql->prepareStatementForSqlObject($update)->execute();
    }
}

