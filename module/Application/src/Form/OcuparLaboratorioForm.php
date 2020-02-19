<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class OcuparLaboratorioForm extends Form
{
    private $laboratoristaModel;
    private $checkReserva;
    private $checkAgendamento;
    
    public function __construct(\Application\Model\LaboratoristaModel $laboratoristaModel)
    {
        $this->laboratoristaModel = $laboratoristaModel;
        
        parent::__construct('ocupar-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();
    }    
    
    public function setData($data)
    {
        if(isset($data['id_laboratorio']) && isset($data['data']) && isset($data['horario']))
        {
            $reserva = $this->laboratoristaModel->checkReserva($data['id_laboratorio'], $data['data'], $data['horario']);
            
            if(!empty($reserva))
            {
                $array_status = [
                    'manha' => $reserva['manha'],
                    'tarde' => $reserva['tarde'],
                    'noite' => $reserva['noite']
                ];

                foreach($array_status as $key => $value)
                {
                    if($key == $data['horario'] && $value == 1)
                    {
                        $this->checkReserva = true;
                    }
                }
                
                $agendamento = $this->laboratoristaModel->checkAgendamento($reserva['id_reserva'], $data['horario']);
                
                if(!empty($agendamento))
                {
                    if($agendamento['status'] === 'I')
                    {
                        $this->checkAgendamento = true;
                    }
                }
            }
        }

        parent::setData($data);
    } 

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'hidden',
            'name'  => 'id_laboratorio',
            'attributes' => [
                'id'       => 'id_laboratorio',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'lab',
            'attributes' => [
                'id'       => 'lab',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
            'options' => [
                'label'     => 'Laboratório',
                'icon'      => 'flask',
                'icon-side' => '<strong> LAB </strong>'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'tipo',
            'attributes' => [
                'id'       => 'tipo',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
            'options' => [
                'label' => 'Tipo',
                'icon'  => 'folder'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'descricao',
            'attributes' => [
                'id'       => 'descricao',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
            'options' => [
                'label' => 'Descricao',
                'icon'  => 'file-alt'
            ]
        ]);
        
        $this->add([            
            'type'  => 'date',
            'name'  => 'data',
            'attributes' => [
                'id'        => 'data',
                'class'     => 'form-control',
            ],
            'options' => [
                'label' => 'Data'
            ]
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'horario',
            'attributes' => [
                'id'    => 'horario',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Horários',
                'value_options' => [
                    'manha' => '08:00 ás 12:00',
                    'tarde' => '13:00 ás 17:00',
                    'noite' => '18:00 ás 22:00'
                ],
            ],
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'status',
            'attributes' => [
                'id'    => 'status',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    0 => 'Disponível',
                    2 => 'Indisponível'
                ],
            ],
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name'  => 'voltar',
            'attributes' => [
                'id'      => 'voltar',
                'class'   => 'btn btn-sm btn-default',
                'onclick' => 'window.location=\'/laboratorista/\laboratorios\''
            ],
            'options' => [
                'label' => 'Voltar'
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'salvar',
            'attributes' => [
                'id' => 'salvar',
            ],
            'options' => [
                'label' => 'Salvar'
            ]
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
            'name'     => 'id_laboratorio',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name'     => 'lab',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'Digits'
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 2
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'tipo',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 100
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'descricao',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 100
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'data',
            'required' => true,
            'validators' => [
                [
                    'name'    => 'Date',
                    'options' => [
                        'format' => "Y-m-d"
                    ]
                ],
            ]
        ]);
        
        $inputFilter->add([
            'name'     => 'horario',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'Callback',
                    'options' => [
                        'message'  => 'Não é possível escolher esse horário pois já se encontra ocupado.',
                        'callback' => function() 
                        {
                            if(!empty($this->checkReserva) && $this->checkReserva)
                            {
                                return false;
                            }

                            return true;
                        }
                    ]
                ],
            ]
        ]);
        
        $inputFilter->add([
            'name'     => 'status',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'Callback',
                    'options' => [
                        'message'  => 'Não é possível mudar o status pois há um agendamento e a reserva já foi alterada.',
                        'callback' => function() 
                        {
                            if(!empty($this->checkAgendamento) && $this->checkAgendamento)
                            {
                                return false;
                            }

                            return true;
                        }
                    ]
                ],
            ]
        ]);
    }        
}