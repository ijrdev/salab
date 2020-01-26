<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class AvisoForm extends Form
{
    public function __construct()
    {
        parent::__construct('aviso-form');
     
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'textarea',
            'name'  => 'mensagem',
            'attributes' => [
                'id'        => 'mensagem',
                'class'     => 'form-control',
                'form'      => 'aviso-form',
                'maxlength' => 350
            ],
            'options' => [
                'label' => 'Mensagem',
                'icon'  => 'comment',
                'rows'  => 7,
                'cols'  => 50
            ]
        ]);
        
        $this->add([            
            'type'  => 'file',
            'name'  => 'anexo',
            'attributes' => [
                'id'       => 'anexo',
                'class'    => 'custom-file-input',
                'multiple' => 'multiple'
            ],
            'options' => [
                'label' => 'Anexo',
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
            'name'  => 'enviar',
            'attributes' => [                
                'id'    => 'enviar',
                'class' => 'btn btn-sm btn-primary',
                'value' => 'Enviar'
            ],
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
            'name'     => 'mensagem',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 10,
                        'max' => 350
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'anexo',
            'required' => false,
            'validators' => [
                [
                    'name' => 'FileSize',
                    'options' => [
                        'message' => 'Tamanho máximo permitido de 500KB',
                        'max' => '500KB'
                    ]
                ],
                [
                    'name' => 'FileMimeType',
                    'options' => [
                        'message' => 'Apenas imagens nos formatos: jpeg, jpg e png',
                        'enableHeaderCheck' => true,
                        'mimeType'          => ["image/jpeg", "image/jpg", 'image/png']
                    ]
                ],
                [
                    'name'    => 'Callback',
                    'options' => [
                        'message'  => 'O máximo é de 4 arquivos',
                        'callback' => function($arquivos, $arr) 
                        {
                            if(count($arr['anexo']) > 4)
                            {
                                return false;
                            }

                            return true;
                        }
                    ]
                ]
            ],
        ]);
    }        
}