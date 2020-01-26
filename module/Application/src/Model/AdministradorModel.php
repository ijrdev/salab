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
                'nome'      => $post['nome'],
                'sobrenome' => $post['sobrenome'],
                'email'     => $post['email'],
                'senha'     => $newPassword,
                'id_grupo'  => $post['grupo'],
                'dthr_cad'  => date('Y-m-d H:i:s')
            ]);
        
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
    
    public function getAllUsers($page, $search, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->notEqualTo('id_usuario', $id_usuario);
        
        if(isset($search) && !empty($search))
        {
            $where->like('id_usuario', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('matricula', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('nome', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('sobrenome', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('email', '%' . strip_tags(trim($search)) . '%');
        }
        
        $select = $sql
            ->select('tb_usuarios')
            ->columns([
                'id_usuario',
                'matricula',
                'nome',
                'sobrenome',
                'email',
                'id_grupo',
                'dthr_cad',
                'dthr_ult_acesso',
                'dthr_ult_alteracao'
            ])
            ->where($where);
        
        $pag_adapter = new DbSelect($select, $sql);
        $paginator   = new Paginator($pag_adapter);

        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        return $paginator;
    }
    
    public function getCountAllUsers()
    {
        $sql = new Sql($this->db);
        
        $select = $sql->select('tb_usuarios');
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getUser($id_usuario)
    {
        $sql = new Sql($this->db);

        $select = $sql
            ->select('tb_usuarios')
            ->columns([
                'id_usuario',
                'matricula',
                'nome',
                'sobrenome',
                'email',
                'id_grupo',
                'dthr_cad',
                'dthr_ult_acesso',
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
                'nome'               => $post['nome'],
                'sobrenome'          => $post['sobrenome'],
                'senha'              => $newPassword,
                'id_grupo'           => $post['grupo'],
                'dthr_ult_alteracao' => date('Y-m-d H:i:s')
            ])
            ->where(['id_usuario' => $id_usuario]);
        
        $sql->prepareStatementForSqlObject($update)->execute();
    }
    
    public function updatePerfil($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $bcrypt      = new Bcrypt();
        $newPassword = $bcrypt->create($post['nova-senha']); 

        $update = $sql
            ->update('tb_usuarios')
            ->set([
                'nome'               => $post['nome'],
                'sobrenome'          => $post['sobrenome'],
                'senha'              => $newPassword,
                'id_grupo'           => $post['grupo'],
                'dthr_ult_alteracao' => date('Y-m-d H:i:s')
            ])
            ->where(['id_usuario' => $id_usuario]);
        
        $sql->prepareStatementForSqlObject($update)->execute();
    }
    
    public function delete($post)
    {
        $sql = new Sql($this->db);
        
        $result = $this->getUser($post['id_usuario']);

        $delete = $sql
            ->delete('tb_usuarios')
            ->where(['id_usuario' => $post['id_usuario']]);
        
        $sql->prepareStatementForSqlObject($delete)->execute();
    }
    
    public function aviso($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $insertAviso = $sql
            ->insert('tb_avisos')
            ->values([
                'id_usuario' => $id_usuario,
                'mensagem'   => $post['mensagem'],
                'dthr_aviso' => date('Y-m-d H:i:s'),
            ]);

        $id_aviso = $sql->prepareStatementForSqlObject($insertAviso)->execute()->getGeneratedValue();
        
        if(isset($post['anexo']) && !empty($post['anexo']))
        {
            foreach($post['anexo'] as $anexo)
            {
                if(isset($anexo['error']) && $anexo['error'] == 0)
                {
                    $fileName = $anexo['name'];
                    $tmpName  = $anexo['tmp_name'];
                    $fileSize = $anexo['size'];
                    $fileType = $anexo['type'];
                    $ext      = pathinfo($fileName, PATHINFO_EXTENSION);
                    
                    $insertAnexo = $sql->insert('tb_anexos')->values([
                        'id_usuario' => $id_usuario,
                        'nome'       => $fileName,
                        'extensao'   => $ext,
                        'tipo'       => $fileType,
                        'tamanho'    => $fileSize,
                        'arquivo'    => file_get_contents($tmpName),
                        'dthr_cad'   => date('Y-m-d H:i:s')
                    ]);

                    $id_anexo = $sql->prepareStatementForSqlObject($insertAnexo)->execute()->getGeneratedValue();
                    
                    $insertAvisoAnexo = $sql->insert('tb_avisos_anexos')->values([
                        'id_aviso' => $id_aviso,
                        'id_anexo' => $id_anexo,
                    ]);

                    $sql->prepareStatementForSqlObject($insertAvisoAnexo)->execute();
                }
            }
        }
    }
}

