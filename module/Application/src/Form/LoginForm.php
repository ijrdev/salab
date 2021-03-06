<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login-form');
     
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
                'id'           => 'matricula',
                'class'        => 'form-control',
                'autocomplete' => 'on',
                'placeholder'  => 'Matrícula'
            ],
            'options' => [
                'icon'  => 'user'
            ]
        ]);
        
        $this->add([            
            'type' => 'password',
            'name' => 'senha',
            'attributes' => [
                'id'           => 'senha',
                'class'        => 'form-control',
                'autocomplete' => 'off',
                'placeholder'  => 'Senha'
            ],
            'options' => [
                'icon'  => 'lock'
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'entrar',
            'attributes' => [
                'id' => 'entrar',
            ],
            'options' => [
                'label' => 'Entrar'
            ]
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