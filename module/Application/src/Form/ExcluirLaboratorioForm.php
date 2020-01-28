<?php

namespace Application\Form;

use Laminas\Form\Form;

class ExcluirLaboratorioForm extends Form
{
    public function __construct()
    {
        parent::__construct('excluir-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
    }

    protected function addElements() 
    {
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
}