<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class ReservarLaboratorioForm extends Form
{
    private $laboratorioModel;
    
    public function __construct(\Application\Model\LaboratoristaModel $laboratorioModel)
    {
        $this->laboratorioModel = $laboratorioModel;
        
        parent::__construct('reservar-laboratorio-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'select',
            'name'  => 'laboratorio',
            'attributes' => [
                'id'    => 'laboratorio',
                'class' => 'custom-select',
            ],
            'options' => [
                'label' => 'Laboratórios',
                'value_options' => $this->laboratorioModel->getLabors()
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
            'type'  => 'text',
            'name'  => 'disciplina',
            'attributes' => [
                'id'    => 'disciplina',
                'class' => 'form-control',
            ],
            'options' => [
                'label'     => 'Disciplina',
                'icon'      => 'book',
            ]
        ]);
        
        $this->add([            
            'type'  => 'textarea',
            'name'  => 'observacao',
            'attributes' => [
                'id'        => 'observacao',
                'class'     => 'form-control',
                'form'      => 'reservar-laboratorio-form',
                'maxlength' => 200
            ],
            'options' => [
                'label' => 'Observação',
                'icon'  => 'comment',
                'rows'  => 7,
                'cols'  => 50
            ]
        ]);
        
        $this->add([
            'type'  => 'Button',
            'name' => 'reservar',
            'attributes' => [
                'id' => 'reservar',
            ],
            'options' => [
                'label' => 'Reservar'
            ]
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
            'name'     => 'laboratorio',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'data',
            'required' => true,
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
            'name'     => 'disciplina',
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
                        'max' => 50
                    ],
                ]
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'observacao',
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
                        'max' => 200
                    ],
                ]
            ],
        ]);
    }        
}