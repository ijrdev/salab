<?php

namespace Application\Model;

use Application\Adapter\Db;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Paginator;

date_default_timezone_set('America/Sao_Paulo');

class ProfessorModel
{
    private $db;

    public function __construct(Db $db)
    {
       $this->db = $db->salab;
    }
    
    public function getAllAgendamentos($page, $search, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('a.id_usuario', $id_usuario);
        
        if(isset($search) && !empty($search))
        {
            $where->like('a.id_agendamento', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('r.dt_reserva', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('a.horario', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('l.lab', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('l.tipo', '%' . strip_tags(trim($search)) . '%');
        }
        
        $select = $sql
            ->select(['a' => 'tb_agendamentos'])
            ->columns([
                'id_agendamento',
                'horario',
                'disciplina',
                'observacao',
                'status',
                'dt_agendamento'
            ])
            ->join(['r' => 'tb_reservas'], 'a.id_reserva = r.id_reserva', ['id_laboratorio'])
            ->join(['l' => 'tb_laboratorios'], 'l.id_laboratorio = r.id_laboratorio', ['lab', 'tipo'])
            ->where($where)
            ->order('a.id_agendamento DESC');
        
        $result = $sql->prepareStatementForSqlObject($select)->execute();
        
        $arr_agen = [];

        foreach($result as $value) 
        {
            $arr_agen[] = $value;
        }

        $pag_adapter = new ArrayAdapter($arr_agen);
        $paginator   = new Paginator($pag_adapter);

        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        return $paginator;
    }
}