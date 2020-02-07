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
    
    // O param retorna todos os laboratórios listado juntamente com sua resenha.
    // Sem o param retorna normalmente os laboratórios cadastrados.
    public function getAllLabors($page, $search, $param = '')
    {
        $sql = new Sql($this->db);
        
        if(!empty($param) && $param === 'param')
        {
            $where = new Where();
            $where->equalTo('r.dt_reserva', isset($search) && !empty($search) ? strip_tags(trim($search)) : date('Y-m-d'));

            $select = $sql
                ->select(['l' => 'tb_laboratorios'])
                ->columns([
                    'id_laboratorio',
                    'lab',
                    'tipo',
                    'descricao',
                ])
                ->join(['r' => 'tb_reservas'], 'l.id_laboratorio = r.id_laboratorio', ['id_reserva', 'dt_reserva', 'manha', 'tarde', 'noite'], 'LEFT')
                ->where($where)
                ->order('l.id_laboratorio ASC');

            $pag_adapter = new DbSelect($select, $sql);
            $paginator   = new Paginator($pag_adapter);

            $paginator->setDefaultItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);

            return $paginator;
        }
        
        $select = $sql->select('tb_laboratorios');
        
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
    
    // Pega a reserva do laboratório do dia e turno já setado para alterar.
    public function getReserva($id_reserva)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('r.id_reserva', $id_reserva);
        
        $select = $sql
            ->select(['r' => 'tb_reservas'])
            ->columns(['id_reserva', 'dt_reserva'])
            ->join(['l' => 'tb_laboratorios'], 'r.id_laboratorio = l.id_laboratorio', ['lab', 'tipo'])
            ->join(['a' => 'tb_agendamentos'], 'r.id_reserva = a.id_reserva', ['horario', 'observacao'])
            ->where($where);
            
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    // Pega todas as reservas do laboratório na data espeficada.
    public function getLaboratorioReserva($id_laboratorio, $dt_reserva)
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
    
    public function reservar($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $reserva = $this->getLaboratorioReserva($post['laboratorio'], $post['data']);
        
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
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $reserva['id_reserva'],
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    break;
                case 'tarde':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'tarde' => 1
                        ])
                        ->where($where);
                    $sql->prepareStatementForSqlObject($update)->execute();
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $reserva['id_reserva'],
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    break;
                case 'noite':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'noite' => 1
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $reserva['id_reserva'],
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
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

                    $id_reserva = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $id_reserva,
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    break;
                case 'tarde':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'tarde' => 1
                        ]);

                    $id_reserva = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $id_reserva,
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    break;
                case 'noite':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'noite' => 1
                        ]);

                    $id_reserva = $sql->prepareStatementForSqlObject($insert)->execute()->getGeneratedValue();
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva' => $id_reserva,
                            'id_usuario' => $id_usuario,
                            'horario'    => $post['horario'],
                            'disciplina' => $post['disciplina'],
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    break;
            }
        }
    }
    
    public function alterarReserva($post, $id_reserva, $horario)
    {
        $sql = new Sql($this->db);
        
        switch($post['check_status']) 
        {
            case 'D':
                $update = $sql
                    ->update('tb_reservas')
                    ->set([
                        $horario => 1
                    ])
                    ->where(['id_reserva' => $id_reserva]);

                $sql->prepareStatementForSqlObject($update)->execute();
                
                $where = new Where();
                $where->equalTo('id_reserva', $id_reserva)
                      ->AND
                      ->equalTo('horario', $horario);

                $updateAgendamento = $sql
                    ->update('tb_agendamentos')
                    ->set([
                        'status'     => $post['check_status'],
                        'observacao' => $post['observacao']
                    ])
                    ->where($where);

                $sql->prepareStatementForSqlObject($updateAgendamento)->execute();
                break;
            case 'O':
                $update = $sql
                    ->update('tb_reservas')
                    ->set([
                        $horario => 1
                    ])
                    ->where(['id_reserva' => $id_reserva]);

                $sql->prepareStatementForSqlObject($update)->execute();
                
                $where = new Where();
                $where->equalTo('id_reserva', $id_reserva)
                      ->AND
                      ->equalTo('horario', $horario);

                $updateAgendamento = $sql
                    ->update('tb_agendamentos')
                    ->set([
                        'status'     => $post['check_status'],
                        'observacao' => $post['observacao']
                    ])
                    ->where($where);

                $sql->prepareStatementForSqlObject($updateAgendamento)->execute();
                break;
            case 'I':
                $update = $sql
                    ->update('tb_reservas')
                    ->set([
                        $horario => 2
                    ])
                    ->where(['id_reserva' => $id_reserva]);

                $sql->prepareStatementForSqlObject($update)->execute();
                
                $where = new Where();
                $where->equalTo('id_reserva', $id_reserva)
                      ->AND
                      ->equalTo('horario', $horario);

                $updateAgendamento = $sql
                    ->update('tb_agendamentos')
                    ->set([
                        'status'     => $post['check_status'],
                        'observacao' => $post['observacao']
                    ])
                    ->where($where);

                $sql->prepareStatementForSqlObject($updateAgendamento)->execute();
                break;
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
        
        $where = new Where();

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