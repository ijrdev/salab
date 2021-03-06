<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class AlterarLaboratorioForm extends Form
{
    public function __construct()
    {
        parent::__construct('alterar-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'text',
            'name'  => 'lab',
            'attributes' => [
                'id'          => 'lab',
                'class'       => 'form-control',
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
                'id'    => 'descricao',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Descricao',
                'icon'  => 'file-alt'
            ]
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'ativar',
            'attributes' => [
                'id'    => 'ativar',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Situação Laboratório',
                'value_options' => [
                    'A' => 'Ativar',
                ],
            ],
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name'  => 'voltar',
            'attributes' => [
                'id'      => 'voltar',
                'class'   => 'btn btn-sm btn-default',
                'onclick' => 'window.location=\'/administrador/\consultar-laboratorios\''
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
            'required' => true,
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
                        'max' => 100
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'descricao',
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
                        'max' => 100
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'ativar',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }        
}