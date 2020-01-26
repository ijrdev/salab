<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class ExcluirLaboratorioForm extends Form
{
    public function __construct()
    {
        parent::__construct('excluir-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'hidden',
            'name'  => 'id_laboratorio',
            'attributes' => [
                'class'  => 'form-control',
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
            'type'  => 'submit',
            'name'  => 'excluir',
            'attributes' => [                
                'id'    => 'excluir',
                'class' => 'btn btn-sm btn-danger',
                'value' => 'Excluir'
            ],
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
            'name'     => 'id_laboratorio',
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
                        'min' => 1,
                        'max' => 3
                    ],
                ],
            ]
        ]);
    }        
}