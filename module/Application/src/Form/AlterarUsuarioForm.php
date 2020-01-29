<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class AlterarUsuarioForm extends Form
{
    private $administradorModel;
    
    public function __construct(\Application\Model\AdministradorModel $administradorModel)
    {
        $this->administradorModel = $administradorModel;
        
        parent::__construct('alterar-usuario-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'text',
            'name'  => 'nome',
            'attributes' => [
                'id'          => 'nome',
                'class'       => 'form-control',
            ],
            'options' => [
                'label' => 'Nome',
                'icon'  => 'user'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'sobrenome',
            'attributes' => [
                'id'          => 'sobrenome',
                'class'       => 'form-control',
            ],
            'options' => [
                'label' => 'Sobrenome',
                'icon'  => 'user'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'matricula',
            'attributes' => [
                'id'       => 'matricula',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
            'options' => [
                'label' => 'Matrícula',
                'icon'  => 'address-card'
            ]
        ]);
        
        $this->add([
            'type'  => 'radio',
            'name'  => 'grupo',
            'attributes' => [
                'id'    => 'grupo',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Grupo do Usuário',
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
                'id'       => 'email',
                'class'    => 'form-control',
                'readonly' => 'readonly',
            ],
            'options' => [
                'label' => 'Email',
                'icon'  => 'envelope'
            ]
        ]);
        
        $this->add([            
            'type' => 'password',
            'name' => 'nova-senha',
            'attributes' => [
                'id'    => 'nova-senha',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Nova Senha',
                'icon'  => 'lock'
            ]
        ]);
        
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
            'name'     => 'nome',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 20
                    ],
                ],
            ]
        ]);
                
        $inputFilter->add([
            'name'     => 'sobrenome',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 20
                    ],
                ],
            ]
        ]);
                
        $inputFilter->add([
            'name'     => 'matricula',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'Digits'
                ],
                [
                    'name' => 'Callback',
                        'options' => [
                            'message'  => 'Matrícula já cadastrada no sistema.',
                            'callback' => function($matricula) 
                            {
                                $result = [];
            
                                try 
                                {
                                    $result = $this->administradorModel->getMatricula($matricula);
                                }
                                catch (\Exception $ex)
                                {
                                    return false;
                                }
                                
                                if(!empty($result))
                                {
                                    if($result['matricula'] == $matricula)
                                    {
                                        return true;
                                    }
                                    
                                    return false;
                                }
                                
                                return true;
                            }
                        ]
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
                [
                    'name' => 'Callback',
                        'options' => [
                            'message'  => 'Email já cadastrado no sistema.',
                            'callback' => function($email) 
                            {
                                $result = [];
            
                                try 
                                {
                                    $result = $this->administradorModel->getEmail($email);
                                }
                                catch (\Exception $ex)
                                {
                                    return false;
                                }
                                
                                if(!empty($result))
                                {
                                    if($result['email'] == $email)
                                    {
                                        return true;
                                    }
                                    
                                    return false;
                                }
                                
                                return true;
                            }
                        ]
                ]
            ]
        ]);    
        
        $inputFilter->add([
            'name'     => 'grupo',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
        
        $inputFilter->add([
            'name'     => 'nova-senha',
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