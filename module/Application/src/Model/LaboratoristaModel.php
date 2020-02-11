<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

date_default_timezone_set('America/Sao_Paulo');

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
                'class'     => __CLASS__,
                'action'     => 'add',
                'sql'     => $sql->buildSqlString($insert)
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
            
            if(isset($search) && !empty($search))
            {
                $where->equalTo('r.dt_reserva', strip_tags(trim($search)));
            }
            else
            {
                $where->equalTo('r.dt_reserva', date('Y-m-d'));
            }

            $select = $sql
                ->select(['l' => 'tb_laboratorios'])
                ->columns([
                    'id_laboratorio',
                    'lab',
                    'tipo',
                ])
                ->join(['r' => 'tb_reservas'], 'l.id_laboratorio = r.id_laboratorio', ['id_reserva', 'dt_reserva'])
                ->join(['a' => 'tb_agendamentos'], 'r.id_reserva = a.id_reserva', ['id_usuario', 'horario', 'observacao', 'status'])
                ->join(['u' => 'tb_usuarios'], 'a.id_usuario = u.id_usuario', ['nome', 'sobrenome'])
                ->where($where)
                ->order('r.id_reserva DESC');
  
            $pag_adapter = new DbSelect($select, $sql);
            $paginator   = new Paginator($pag_adapter);

            $paginator->setDefaultItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);

            return $paginator;
        }
        
        $where = new Where();
        
        $where->like('lab', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('tipo', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('descricao', '%' . strip_tags(trim($search)) . '%');
        
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
    
    public function getCountAllLabors()
    {
        $sql = new Sql($this->db);
        
        $select = $sql->select('tb_laboratorios');
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getCountAllAgendamentos()
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->between('dthr_agendamento', date('Y-m-01') . ' 00:00:00', date('Y-m-31') . ' 23:59:59');
        
        $select = $sql
            ->select('tb_agendamentos')
            ->where($where);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getAgendamento($id_agendamento)
    {
        $sql = new Sql($this->db);
        
        $select = $sql
            ->select('tb_agendamentos')
            ->where(['id_agendamento' => $id_agendamento]);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function cancelarAgendamento($id_agendamento, $id_reserva, $horario, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $select = $sql
            ->select('tb_reservas')
            ->where(['id_reserva' => $id_reserva]);
        
        $reserva = $sql->prepareStatementForSqlObject($select)->execute()->current();
        
        if(isset($reserva) && !empty($reserva))
        {
            if($reserva[$horario] == 1)
            {
                $update = $sql
                    ->update('tb_reservas')
                    ->set([$horario => 0])
                    ->where(['id_reserva' => $reserva['id_reserva']]);

                $sql->prepareStatementForSqlObject($update)->execute();
            }

            $delete = $sql
                ->delete('tb_agendamentos')
                ->where(['id_agendamento' => $id_agendamento]);

            $sql->prepareStatementForSqlObject($delete)->execute();
            
            $insertLog = $sql
                ->insert('tb_logs')
                ->values([
                    'id_usuario' => $id_usuario,
                    'dthr_log'   => date('Y-m-d H:i:s'),
                    'class'     => __CLASS__,
                    'action'     => 'cancelar-agendamento',
                    'sql'     => $sql->buildSqlString($delete)
                ]);

            $sql->prepareStatementForSqlObject($insertLog)->execute();
        }
    }
    
    public function ocuparLaboratorio($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $reserva = $this->getLaboratorioReserva($post['id_laboratorio'], $post['data']);
        
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
                            'manha' => $post['status']
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
                case 'tarde':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'tarde' => $post['status']
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
                case 'noite':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'noite' => $post['status']
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    break;
            }
            
            $insertLog = $sql
                ->insert('tb_logs')
                ->values([
                    'id_usuario' => $id_usuario,
                    'dthr_log'   => date('Y-m-d H:i:s'),
                    'class'     => __CLASS__,
                    'action'     => 'ocupar-laboratorio',
                    'sql'     => $sql->buildSqlString($update)
                ]);

            $sql->prepareStatementForSqlObject($insertLog)->execute();
        }
        else
        {
            switch($post['horario'])
            {
                case 'manha':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['id_laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'manha'          => $post['status']
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
                case 'tarde':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['id_laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'tarde'          => $post['status']
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
                case 'noite':
                    $insert = $sql
                        ->insert('tb_reservas')
                        ->values([
                            'id_laboratorio' => $post['id_laboratorio'],
                            'dt_reserva'     => $post['data'],
                            'noite'          => $post['status']
                        ]);

                    $sql->prepareStatementForSqlObject($insert)->execute();
                    break;
            }
            
            $insertLog = $sql
                ->insert('tb_logs')
                ->values([
                    'id_usuario' => $id_usuario,
                    'dthr_log'   => date('Y-m-d H:i:s'),
                    'class'     => __CLASS__,
                    'action'     => 'ocupar-laboratorio',
                    'sql'     => $sql->buildSqlString($insert)
                ]);

            $sql->prepareStatementForSqlObject($insertLog)->execute();
        }
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
    
    public function checkAgendamento($id_reserva, $horario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('id_reserva', $id_reserva)
              ->AND
              ->equalTo('horario', $horario);
        
        $select = $sql
            ->select('tb_agendamentos')
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
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($update)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $reserva['id_reserva'],
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
                    break;
                case 'tarde':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'tarde' => 1
                        ])
                        ->where($where);
                    $sql->prepareStatementForSqlObject($update)->execute();
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($update)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $reserva['id_reserva'],
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
                    break;
                case 'noite':
                    $update = $sql
                        ->update('tb_reservas')
                        ->set([
                            'noite' => 1
                        ])
                        ->where($where);

                    $sql->prepareStatementForSqlObject($update)->execute();
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($update)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $reserva['id_reserva'],
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
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
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insert)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $id_reserva,
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
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
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insert)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $id_reserva,
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
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
                    
                    $insertLog = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insert)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLog)->execute();
                    
                    // ---------------------------------------------------------
                    
                    $insertAgendamento = $sql
                        ->insert('tb_agendamentos')
                        ->values([
                            'id_reserva'       => $id_reserva,
                            'id_usuario'       => $id_usuario,
                            'horario'          => $post['horario'],
                            'disciplina'       => $post['disciplina'],
                            'dthr_agendamento' => date('Y-m-d H:i:s')
                        ]);

                    $sql->prepareStatementForSqlObject($insertAgendamento)->execute();
                    
                    $insertLogAgendamento = $sql
                        ->insert('tb_logs')
                        ->values([
                            'id_usuario' => $id_usuario,
                            'dthr_log'   => date('Y-m-d H:i:s'),
                            'class'     => __CLASS__,
                            'action'     => 'reservar',
                            'sql'     => $sql->buildSqlString($insertAgendamento)
                        ]);

                    $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
                    break;
            }
        }
    }
    
    public function alterarReserva($post, $id_reserva, $horario, $id_usuario)
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
                
                $insertLog = $sql
                    ->insert('tb_logs')
                    ->values([
                        'id_usuario' => $id_usuario,
                        'dthr_log'   => date('Y-m-d H:i:s'),
                        'class'     => __CLASS__,
                        'action'     => 'alterar-reserva',
                        'sql'     => $sql->buildSqlString($update)
                    ]);

                $sql->prepareStatementForSqlObject($insertLog)->execute();
                
                // -------------------------------------------------------------
                
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
                
                $insertLogAgendamento = $sql
                    ->insert('tb_logs')
                    ->values([
                        'id_usuario' => $id_usuario,
                        'dthr_log'   => date('Y-m-d H:i:s'),
                        'class'     => __CLASS__,
                        'action'     => 'alterar-reserva',
                        'sql'     => $sql->buildSqlString($updateAgendamento)
                    ]);

                $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
                break;
            case 'I':
                $update = $sql
                    ->update('tb_reservas')
                    ->set([
                        $horario => 2
                    ])
                    ->where(['id_reserva' => $id_reserva]);

                $sql->prepareStatementForSqlObject($update)->execute();
                
                $insertLog = $sql
                    ->insert('tb_logs')
                    ->values([
                        'id_usuario' => $id_usuario,
                        'dthr_log'   => date('Y-m-d H:i:s'),
                        'class'     => __CLASS__,
                        'action'     => 'alterar-reserva',
                        'sql'     => $sql->buildSqlString($update)
                    ]);

                $sql->prepareStatementForSqlObject($insertLog)->execute();
                
                // -------------------------------------------------------------
                
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
                
                $insertLogAgendamento = $sql
                    ->insert('tb_logs')
                    ->values([
                        'id_usuario' => $id_usuario,
                        'dthr_log'   => date('Y-m-d H:i:s'),
                        'class'     => __CLASS__,
                        'action'     => 'alterar-reserva',
                        'sql'     => $sql->buildSqlString($updateAgendamento)
                    ]);

                $sql->prepareStatementForSqlObject($insertLogAgendamento)->execute();
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
                'class'     => __CLASS__,
                'action'     => 'update',
                'sql'     => $sql->buildSqlString($update)
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
                'class'     => __CLASS__,
                'action'     => 'delete',
                'sql'     => $sql->buildSqlString($delete)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
    }
}