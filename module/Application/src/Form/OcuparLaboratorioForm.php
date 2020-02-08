<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class OcuparLaboratorioForm extends Form
{
    public function __construct()
    {
        parent::__construct('ocupar-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
    }    
    
/*    public function setData($data) 
    {
        if(!empty($data['faturado']) && $data['faturado'] == 'S')
        {
            $this->faturado = true;
        }
        
        if(!empty($data['pago']) && $data['pago'] == 'S')
        {
            $this->pago = true;
        }
        
        parent::setData($data);
        
        $this->addInputFilter();
    } 
 */

    protected function addElements() 
    {
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
                    'D' => 'Disponível',
                    'I' => 'Indisponível'
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
        ]);
        
        $inputFilter->add([
            'name'     => 'status',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }        
}