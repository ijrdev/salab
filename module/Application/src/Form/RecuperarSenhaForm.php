<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class RecuperarSenhaForm extends Form
{
    public function __construct()
    {
        parent::__construct('recuperar-senha-form');
     
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
            'type'  => 'text',
            'name'  => 'email',
            'attributes' => [
                'id'    => 'email',
                'class' => 'form-control',
                'autocomplete' => 'on',
                'placeholder'  => 'Email'
            ],
            'options' => [
                'icon'  => 'envelope'
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'enviar',
            'attributes' => [
                'id' => 'enviar',
            ],
            'options' => [
                'label' => 'Enviar'
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
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress'
                ],
            ]
        ]);
    }        
}