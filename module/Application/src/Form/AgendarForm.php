<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class AgendarForm extends Form
{
    private $laboratorioModel;
    
    public function __construct(\Application\Model\LaboratoristaModel $laboratorioModel)
    {
        $this->laboratorioModel = $laboratorioModel;
        
        parent::__construct('agendar-form');
     
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
            'type'  => 'Button',
            'name' => 'agendar',
            'attributes' => [
                'id' => 'agendar',
            ],
            'options' => [
                'label' => 'Agendar'
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
    }        
}