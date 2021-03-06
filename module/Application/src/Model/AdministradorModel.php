<?php

namespace Application\Model;

use Application\Adapter\Db;
use Hashids\Hashids;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

date_default_timezone_set('America/Sao_Paulo');

class AdministradorModel
{
    private $db;
    private $sessionModel;

    public function __construct(Db $db, \Application\Model\SessionModel $sessionModel)
    {
       $this->db           = $db->salab;
       $this->sessionModel = $sessionModel;
    }
    
    public function add($post, $id_usuario)
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
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'class'     => __CLASS__,
                'action'     => 'cadastrar-usuario',
                'sql'     => $sql->buildSqlString($insert)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
    }
    
    public function getAllUsers($page, $search, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->notEqualTo('id_usuario', $id_usuario);
        
        if($this->sessionModel->getUsuario()['id_usuario'] != 1)
        {
            $where->notEqualTo('id_grupo', 1);
        }
        
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
                'dthr_ult_alteracao',
                'situacao'
            ])
            ->where($where)
            ->order('situacao');
        
        $result = $sql->prepareStatementForSqlObject($select)->execute();
        
        $arr_user = [];

        foreach($result as $value) 
        {
            $arr_user[] = $value;
        }

        $pag_adapter = new ArrayAdapter($arr_user);
        $paginator   = new Paginator($pag_adapter);

        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        return $paginator;
    }
    
    public function getAllAgendamentos($page, $search, $data)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
                
        if(isset($search) && !empty($search))
        {
            $where->like('a.id_agendamento', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('a.horario', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('l.lab', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('l.tipo', '%' . strip_tags(trim($search)) . '%')
                ->OR
                ->like('a.status', '%' . strip_tags(trim($search)) . '%');
        }
        
        if(isset($data) && !empty($data))
        {
            $where->equalTo('dt_agendamento', trim($data));
        }
        
        $select = $sql
            ->select(['a' => 'tb_agendamentos'])
            ->columns([
                'id_agendamento',
                'disciplina',
                'horario',
                'status',
                'dt_agendamento'
            ])
            ->join(['r' => 'tb_reservas'], 'a.id_reserva = r.id_reserva', ['id_laboratorio'])
            ->join(['l' => 'tb_laboratorios'], 'l.id_laboratorio = r.id_laboratorio', ['lab', 'tipo'])
            ->join(['u' => 'tb_usuarios'], 'a.id_usuario = u.id_usuario', ['nome', 'sobrenome'])
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
                'dthr_ult_alteracao',
                'situacao'
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
    
    public function getAllAvisos()
    {       
        $sql = new Sql($this->db);
        
        $select = $sql
            ->select(['a' => 'tb_avisos'])
            ->join(['u'   => 'tb_usuarios'], 'a.id_usuario = u.id_usuario', ['nome', 'sobrenome',' email'])
            ->order('a.id_aviso DESC')
            ->limit(10);
        
        $result = $sql->prepareStatementForSqlObject($select)->execute();
        
        $avisos = [];
        
        foreach($result as $value) 
        {
            $value['foto_perfil']  = $this->getFotoPerfil($value['id_usuario']);
            
            $value['anexos_aviso'] = $this->getAllAnexosAviso($value['id_aviso']);

            $avisos[] = $value;
        }
        
        return $avisos;
    }
    
    public function getCountAllAvisos()
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->between('dthr_aviso', date('Y-m-01') . ' 00:00:00', date('Y-m-31') . ' 23:59:59');
        
        $select = $sql
            ->select('tb_avisos')
            ->where($where);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->count();
    }
    
    public function getFotoPerfil($id_usuario)
    {
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('a.id_usuario', $id_usuario)
              ->AND
              ->equalTo('a.segmento', 'PRF');
        
        $select = $sql
            ->select(['a' => 'tb_anexos'])
            ->where($where);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function getAllAnexosAviso($id_aviso)
    {
        $hashid = new Hashids('id', 10);
        
        $sql = new Sql($this->db);
        
        $where = new Where();
        $where->equalTo('aa.id_aviso', $id_aviso);
        
        $select = $sql
            ->select(['aa' => 'tb_avisos_anexos'])
            ->join(['an'  => 'tb_anexos'], 'aa.id_anexo = an.id_anexo', '*', 'LEFT')
            ->where($where);
        
        $anexos_aviso = $sql->prepareStatementForSqlObject($select)->execute();
        
        $assist     = [];
        $arr_anexos = [];
        
        foreach($anexos_aviso as $anexo) 
        {
            $assist[] = $anexo;
        }
        
        if(!empty($assist))
        {
            foreach($assist as $value) 
            {
                $value['id_anexo'] = $hashid->encode($value['id_anexo'], date('YmdHis'));
                
                $arr_anexos[] = $value;
            }
        }
        
        return $arr_anexos;
    }
    
    public function getAnexo($id_anexo)
    {
        $sql = new Sql($this->db);
        
        $select = $sql
            ->select('tb_anexos')
            ->where(['id_anexo' => $id_anexo]);
        
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function update($post, $id_usuario_update, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        if(isset($post['ativar']) && !empty($post['ativar']))
        {
            $bcrypt      = new Bcrypt();
            $newPassword = $bcrypt->create($post['nova-senha']); 

            $update = $sql
                ->update('tb_usuarios')
                ->set([
                    'nome'               => $post['nome'],
                    'sobrenome'          => $post['sobrenome'],
                    'senha'              => $newPassword,
                    'id_grupo'           => $post['grupo'],
                    'dthr_ult_alteracao' => date('Y-m-d H:i:s'),
                    'situacao'           => $post['ativar']
                ])
                ->where(['id_usuario' => $id_usuario_update]);

            $sql->prepareStatementForSqlObject($update)->execute();
        }
        else
        {
            $bcrypt      = new Bcrypt();
            $newPassword = $bcrypt->create($post['nova-senha']); 

            $update = $sql
                ->update('tb_usuarios')
                ->set([
                    'nome'               => $post['nome'],
                    'sobrenome'          => $post['sobrenome'],
                    'senha'              => $newPassword,
                    'id_grupo'           => $post['grupo'],
                    'dthr_ult_alteracao' => date('Y-m-d H:i:s'),
                ])
                ->where(['id_usuario' => $id_usuario_update]);

            $sql->prepareStatementForSqlObject($update)->execute();
        }
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'class'     => __CLASS__,
                'action'     => 'alterar-usuario',
                'sql'     => $sql->buildSqlString($update)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
    }
    
    public function perfil($post, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        if(isset($post['nova-senha']) && !empty($post['nova-senha']))
        {           
            $bcrypt      = new Bcrypt();
            $newPassword = $bcrypt->create($post['nova-senha']); 

            $update = $sql
                ->update('tb_usuarios')
                ->set([
                    'nome'               => $post['nome'],
                    'sobrenome'          => $post['sobrenome'],
                    'senha'              => $newPassword,
                    'dthr_ult_alteracao' => date('Y-m-d H:i:s')
                ])
                ->where(['id_usuario' => $id_usuario]);

            $sql->prepareStatementForSqlObject($update)->execute();
            
            if(isset($post['foto']) && !empty($post['foto']))
            {
                if(isset($post['foto']['error']) && $post['foto']['error'] == 0)
                {
                    $fileName = $post['foto']['name'];
                    $tmpName  = $post['foto']['tmp_name'];
                    $fileSize = $post['foto']['size'];
                    $fileType = $post['foto']['type'];
                    $ext      = pathinfo($fileName, PATHINFO_EXTENSION);

                    $where = new Where();
                    $where->equalTo('id_usuario', $id_usuario)
                          ->AND
                          ->equalTo('segmento', 'PRF');

                    $select = $sql
                            ->select('tb_anexos')
                            ->where($where);

                    $result = $sql->prepareStatementForSqlObject($select)->execute()->current();

                    if(!$result)
                    {
                        $insertAnexo = $sql
                            ->insert('tb_anexos')
                            ->values([
                                'id_usuario' => $id_usuario,
                                'segmento'   => 'PRF',
                                'nome_anexo' => $fileName,
                                'extensao'   => $ext,
                                'tipo'       => $fileType,
                                'tamanho'    => $fileSize,
                                'arquivo'    => file_get_contents($tmpName),
                                'dthr_cad'   => date('Y-m-d H:i:s')
                            ]);

                        $sql->prepareStatementForSqlObject($insertAnexo)->execute();
                    }
                    else
                    {
                        $where = new Where();
                        $where->equalTo('id_usuario', $id_usuario)
                              ->AND
                              ->equalTo('segmento', 'PRF');

                        $updateAnexo = $sql
                            ->update('tb_anexos')
                            ->set([
                                'id_usuario' => $id_usuario,
                                'segmento'   => 'PRF',
                                'nome_anexo' => $fileName,
                                'extensao'   => $ext,
                                'tipo'       => $fileType,
                                'tamanho'    => $fileSize,
                                'arquivo'    => file_get_contents($tmpName),
                                'dthr_cad'   => date('Y-m-d H:i:s')
                            ])
                            ->where($where); 

                        $sql->prepareStatementForSqlObject($updateAnexo)->execute();
                    }
                }
            }
        }
        else
        {
            $update = $sql
                ->update('tb_usuarios')
                ->set([
                    'nome'               => $post['nome'],
                    'sobrenome'          => $post['sobrenome'],
                    'id_grupo'           => $post['grupo'],
                    'dthr_ult_alteracao' => date('Y-m-d H:i:s')
                ])
                ->where(['id_usuario' => $id_usuario]);

            $sql->prepareStatementForSqlObject($update)->execute();
            
            if(isset($post['foto']) && !empty($post['foto']))
            {
                if(isset($post['foto']['error']) && $post['foto']['error'] == 0)
                {
                    $fileName = $post['foto']['name'];
                    $tmpName  = $post['foto']['tmp_name'];
                    $fileSize = $post['foto']['size'];
                    $fileType = $post['foto']['type'];
                    $ext      = pathinfo($fileName, PATHINFO_EXTENSION);

                    $where = new Where();
                    $where->equalTo('id_usuario', $id_usuario)
                          ->AND
                          ->equalTo('segmento', 'PRF');

                    $select = $sql
                            ->select('tb_anexos')
                            ->where($where);

                    $result = $sql->prepareStatementForSqlObject($select)->execute()->current();

                    if(!$result)
                    {
                        $insertAnexo = $sql
                            ->insert('tb_anexos')
                            ->values([
                                'id_usuario' => $id_usuario,
                                'segmento'   => 'PRF',
                                'nome_anexo' => $fileName,
                                'extensao'   => $ext,
                                'tipo'       => $fileType,
                                'tamanho'    => $fileSize,
                                'arquivo'    => file_get_contents($tmpName),
                                'dthr_cad'   => date('Y-m-d H:i:s')
                            ]);

                        $sql->prepareStatementForSqlObject($insertAnexo)->execute();
                    }
                    else
                    {
                        $where = new Where();
                        $where->equalTo('id_usuario', $id_usuario)
                              ->AND
                              ->equalTo('segmento', 'PRF');

                        $updateAnexo = $sql
                            ->update('tb_anexos')
                            ->set([
                                'id_usuario' => $id_usuario,
                                'segmento'   => 'PRF',
                                'nome_anexo' => $fileName,
                                'extensao'   => $ext,
                                'tipo'       => $fileType,
                                'tamanho'    => $fileSize,
                                'arquivo'    => file_get_contents($tmpName),
                                'dthr_cad'   => date('Y-m-d H:i:s')
                            ])
                            ->where($where); 

                        $sql->prepareStatementForSqlObject($updateAnexo)->execute();
                    }
                }
            }
        }
    }
    
    public function inativar($id_usuario_inativar, $id_usuario)
    {
        $sql = new Sql($this->db);
        
        $inativarUsuario = $sql
            ->update('tb_usuarios')
            ->set([
                'situacao' => 'I'
            ])
            ->where(['id_usuario' => $id_usuario_inativar]);
        
        $sql->prepareStatementForSqlObject($inativarUsuario)->execute();
        
        $where = new Where();
        $where->equalTo('id_usuario', $id_usuario_inativar)
              ->AND
              ->greaterThanOrEqualTo('dt_agendamento', date('Y-m-d'));
        
        $select = $sql
            ->select('tb_agendamentos')
            ->columns(['id_agendamento', 'id_reserva', 'horario'])
            ->where($where);
        
        $rs = $sql->prepareStatementForSqlObject($select)->execute();
        
        foreach($rs as $value) 
        {
            $cancelarReserva = $sql
                ->update('tb_reservas')
                ->set([
                    $value['horario'] => 0
                ])
                ->where(['id_reserva' => $value['id_reserva']]);
        
            $sql->prepareStatementForSqlObject($cancelarReserva)->execute();
            
            $deleteAgendamentos = $sql
                ->delete('tb_agendamentos')
                ->where(['id_agendamento' => $value['id_agendamento']]);
        
            $sql->prepareStatementForSqlObject($deleteAgendamentos)->execute();
        }
        
        $insertLog = $sql
            ->insert('tb_logs')
            ->values([
                'id_usuario' => $id_usuario,
                'dthr_log'   => date('Y-m-d H:i:s'),
                'class'      => __CLASS__,
                'action'     => 'inativar-usuario',
                'sql'        => $sql->buildSqlString($inativarUsuario)
            ]);
        
        $sql->prepareStatementForSqlObject($insertLog)->execute();
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
                        'segmento'   => 'ANX',
                        'nome_anexo' => $fileName,
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

