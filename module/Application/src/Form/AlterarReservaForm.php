<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class AlterarReservaForm extends Form
{
    public function __construct()
    {
        parent::__construct('alterar-reserva-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'date',
            'name'  => 'dt_reserva',
            'attributes' => [
                'id'        => 'dt_reserva',
                'class'     => 'form-control',
                'readonly'  => 'readonly'
            ],
            'options' => [
                'label' => 'Data'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'lab',
            'attributes' => [
                'id'          => 'lab',
                'class'       => 'form-control',
                'readonly' => 'readonly'
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
                'id'    => 'tipo',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ],
            'options' => [
                'label' => 'Tipo',
                'icon'  => 'folder'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'horario',
            'attributes' => [
                'id'    => 'horario',
                'class'    => 'form-control',
                'readonly' => 'readonly'
            ],
            'options' => [
                'label' => 'Tipo',
                'icon'  => 'clock'
            ]
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'check_status',
            'attributes' => [
                'id'    => 'check_status',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'D' => 'Disponível',
                    'O' => 'Ocupado',
                    'I' => 'Indisponível'
                ],
            ],
        ]);
        
        $this->add([            
            'type'  => 'textarea',
            'name'  => 'observacao',
            'attributes' => [
                'id'        => 'observacao',
                'class'     => 'form-control',
                'form'      => 'alterar-reserva-form',
                'maxlength' => 200
            ],
            'options' => [
                'label' => 'Observação',
                'icon'  => 'comment',
                'rows'  => 7,
                'cols'  => 50
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name'  => 'voltar',
            'attributes' => [
                'id'      => 'voltar',
                'class'   => 'btn btn-sm btn-default',
                'onclick' => 'window.location=\'/laboratorista/\reservas\''
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
            'name'     => 'dt_reserva',
            'required' => false,
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
            'name'     => 'horario',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'check_status',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'observacao',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 200
                    ],
                ]
            ],
        ]);
    }        
}