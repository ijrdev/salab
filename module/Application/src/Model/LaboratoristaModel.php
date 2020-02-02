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
    
    public function add($post, $id_usuario)
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
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'classe'     => __CLASS__,
                'funcao'     => 'add',
                'script'     => $sql->buildSqlString($insert)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
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
    
    public function getLabors()
    {
        $sql = new Sql($this->db);

        $select = $sql->select('tb_laboratorios');
        
        $laboratorios = $sql->prepareStatementForSqlObject($select)->execute();
        
        $array_labors = [];
        
        foreach($laboratorios as $laboratorio) 
        {
            $array_labors[$laboratorio['id_laboratorio']] = $laboratorio['lab'] . ' - ' . $laboratorio['tipo'];
        }
        
        return $array_labors;
    }
    
    public function getLabor($id_laboratorio)
    {
        $sql = new Sql($this->db);

        $select = $sql
            ->select('tb_laboratorios')
            ->where(['id_laboratorio' => $id_laboratorio]);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function getCountAlLabors()
    {
        $sql = new Sql($this->db);
        
        $select = $sql->select('tb_laboratorios');
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getReserva($id_laboratorio, $dt_reserva)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('id_laboratorio', $id_laboratorio)
              ->AND
              ->equalTo('dt_reserva', $dt_reserva);
        
        $select = $sql
            ->select('tb_reservas')
            ->where($where);
            
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function checkReserva($id_laboratorio, $dt_reserva, $horario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('id_laboratorio', $id_laboratorio)
              ->AND
              ->equalTo('dt_reserva', $dt_reserva);
        
        switch($horario) 
        {
            case 'manha':
                $where->greaterThan('manha', 0);
                break;
            case 'tarde':
                $where->greaterThan('tarde', 0);
                break;
            case 'noite':
                $where->greaterThan('noite', 0);
                break;
        }
        
        $select = $sql
            ->select('tb_reservas')
            ->where($where);
            
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function agendar($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $reserva = $this->getReserva($post['laboratorio'], $post['data']);
        
        if(isset($reserva) && !empty($reserva))
        {
            $where = new Where();
            $where->equalTo('id_laboratorio', $reserva['id_laboratorio'])
                  ->AND
                  ->equalTo('dt_reserva', $reserva['dt_reserva']);
            
            switch($post['horario'])
            {
                case 'manha':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'manha' => 1
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
                case 'tarde':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'tarde' => 1
                        ])
                        ->where($where);
                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
                case 'noite':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'noite' => 1
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
            }
        }
        else
        {
            switch($post['horario'])
            {
                case 'manha':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'manha' => 1
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
                case 'tarde':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'tarde' => 1
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
                case 'noite':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'noite' => 1
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
            }
        }
    }
    
    public function update($post, $id_laboratorio, $id_usuario)
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
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'classe'     => __CLASS__,
                'funcao'     => 'update',
                'script'     => $sql->buildSqlString($update)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
    }
    
    public function delete($id_laboratorio, $id_usuario)
    {
        $sql = new Sql($this->db);

        $delete = $sql
            ->delete('tb_laboratorios')
            ->where(['id_laboratorio' => $id_laboratorio]);
        
        $sql->prepareStatementForSqlObject($delete)->execute();
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'classe'     => __CLASS__,
                'funcao'     => 'delete',
                'script'     => $sql->buildSqlString($delete)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
    }
}