<?php

namespace Application\Form;

use Laminas\Form\Form;

class CancelarAgendamentoForm extends Form
{
    public function __construct()
    {
        parent::__construct('cancelar-agendamento-form');
     
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
                'onclick' => 'window.location=\'/professor/\meus-agendamentos\''
            ],
            'options' => [
                'label' => 'Voltar'
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'cancelar',
            'attributes' => [
                'id' => 'cancelar',
            ],
            'options' => [
                'label' => 'Cancelar'
            ]
        ]);
    }
}