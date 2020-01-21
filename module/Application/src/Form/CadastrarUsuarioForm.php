<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class CadastrarUsuarioForm extends Form
{
    public function __construct()
    {
        parent::__construct('cadastrar-usuario-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'text',
            'name'  => 'matricula',
            'attributes' => [
                'id'    => 'matricula',
                'class' => 'form-control form-control-sm',
            ],
            'options' => [
                'label' => 'Matrícula',
            ]
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'tipo',
            'attributes' => [
                'id'    => 'tipo',
                'class' => 'form-control form-control-sm'
            ],
            'options' => [
                'label' => 'Tipo Usuário',
                'value_options' => [
                    1 => 'Administrador',
                    2 => 'Laboratorista',
                    3 => 'Professor'
                ],
            ],
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'email',
            'attributes' => [
                'id'    => 'email',
                'class' => 'form-control form-control-sm',
            ],
            'options' => [
                'label' => 'Email',
            ]
        ]);
        
        $this->add([            
            'type' => 'password',
            'name' => 'senha',
            'attributes' => [
                'id'    => 'senha',
                'class' => 'form-control form-control-sm',
            ],
            'options' => [
                'label' => 'Senha',
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name'  => 'voltar',
            'attributes' => [
                'id'      => 'voltar',
                'class'   => 'btn btn-sm btn-default',
                'onclick' => 'window.location=\'/administrador\''
            ],
            'options' => [
                'label' => 'Voltar'
            ]
        ]);
        
        $this->add([
            'type'  => 'submit',
            'name'  => 'salvar',
            'attributes' => [                
                'id'    => 'salvar',
                'class' => 'btn btn-sm btn-primary',
                'value' => 'Salvar'
            ],
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
                
        $inputFilter->add([
            'name'     => 'matricula',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'Digits'
                ]
            ]
        ]);     
        
        $inputFilter->add([
            'name'     => 'email',
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
                        'max' => 128
                    ],
                ],
                [
                    'name' => 'EmailAddress'
                ],
            ]
        ]);    
        
        $inputFilter->add([
            'name'     => 'tipo',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name'     => 'senha',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 6,
                        'max' => 6
                    ],
                ]
            ],
        ]);
    }        
}