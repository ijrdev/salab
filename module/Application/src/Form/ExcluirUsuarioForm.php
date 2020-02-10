<?php

namespace Application\Form;

use Laminas\Form\Form;

class ExcluirUsuarioForm extends Form
{
    public function __construct()
    {
        parent::__construct('excluir-usuario-form');
     
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
                'onclick' => 'window.location=\'/administrador/\consultar-usuarios\''
            ],
            'options' => [
                'label' => 'Voltar'
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'excluir',
            'attributes' => [
                'id' => 'excluir',
            ],
            'options' => [
                'label' => 'Excluir'
            ]
        ]);
    }
}