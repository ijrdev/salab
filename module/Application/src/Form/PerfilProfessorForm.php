<?php

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

class PerfilProfessorForm extends Form
{
    private $administradorModel;
    
    public function __construct(\Application\Model\AdministradorModel $administradorModel)
    {
        $this->administradorModel = $administradorModel;
        
        parent::__construct('perfil-professor-form');
     
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }

    protected function addElements() 
    {
        $this->add([            
            'type'  => 'file',
            'name'  => 'foto',
            'attributes' => [
                'id'       => 'foto',
                'class'    => 'custom-file-input',
                'accept'   => 'image/png, image/jpeg, image/jpg',
                'onchange' => 'mudaFotoPerfil(this)'
            ]
        ]);
        
        $this->add([            
            'type'  => 'text',
            'name'  => 'nome',
            'attributes' => [
                'id'    => 'nome',
                'class' => 'form-control',
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
            'name' => 'salvar',
            'attributes' => [
                'id' => 'salvar',
            ],
            'options' => [
                'label' => 'Salvar'
            ]
        ]);
    }

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
            'name'     => 'foto',
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
            ],
        ]);
        
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
                        'min' => 1,
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
                        'min' => 1,
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
            'required' => false,
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