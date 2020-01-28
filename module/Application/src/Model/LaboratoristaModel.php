<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

class LaboratoristaModel
{
    private $db;

    public function __construct(Db $db)
    {
       $this->db = $db->salab;
    }
    
    public function add($post)
    {
        $sql = new Sql($this->db);
        
        $insert = $sql
            ->insert('tb_laboratorios')
            ->values([
                'lab'       => 'LAB ' . $post['lab'],
                'tipo'      => $post['tipo'],
                'descricao' => $post['descricao'],
                'dthr_cad'  => date('Y-m-d H:i:s')
            ]);
        
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
    
    public function getAllLabors($page, $search)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        
        if(isset($search) && !empty($search))
        {
            $where->like('id_laboratorio', '%' . strip_tags(trim($search)) . '%')
              ->OR
              ->like('lab', '%' . strip_tags(trim($search)) . '%')
              ->OR
              ->like('tipo', '%' . strip_tags(trim($search)) . '%')
              ->OR
              ->like('descricao', '%' . strip_tags(trim($search)) . '%');
        }
        
        $select = $sql
            ->select('tb_laboratorios')
            ->where($where);
        
        $pag_adapter = new DbSelect($select, $sql);
        $paginator   = new Paginator($pag_adapter);

        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        return $paginator;
    }
    
    public function getCountAlLabors()
    {
        $sql = new Sql($this->db);
        
        $select = $sql->select('tb_laboratorios');
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getLabor($id_laboratorio)
    {
        $sql = new Sql($this->db);

        $select = $sql
            ->select('tb_laboratorios')
            ->where(['id_laboratorio' => $id_laboratorio]);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function update($post, $id_laboratorio)
    {
        $sql = new Sql($this->db);

        $update = $sql
            ->update('tb_laboratorios')
            ->set([
                'lab'       => 'LAB ' . $post['lab'],
                'tipo'      => $post['tipo'],
                'descricao' => $post['descricao']
            ])
            ->where(['id_laboratorio' => $id_laboratorio]);
        
        $sql->prepareStatementForSqlObject($update)->execute();
    }
    
    public function delete($id_laboratorio)
    {
        $sql = new Sql($this->db);

        $delete = $sql
            ->delete('tb_laboratorios')
            ->where(['id_laboratorio' => $id_laboratorio]);
        
        $sql->prepareStatementForSqlObject($delete)->execute();
    }
}