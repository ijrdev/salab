<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Application\Adapter\Db;
use Application\Adapter\Factory\DbFactory;
use Application\Controller\AdministradorController;
use Application\Controller\AuthController;
use Application\Controller\Factory\AdministradorControllerFactory;
use Application\Controller\Factory\AuthControllerFactory;
use Application\Controller\Factory\LaboratoristaControllerFactory;
use Application\Controller\Factory\ProfessorControllerFactory;
use Application\Controller\LaboratoristaController;
use Application\Controller\ProfessorController;
use Application\Model\AuthAdapter;
use Application\Model\AuthModel;
use Application\Model\Factory\AuthAdapterFactory;
use Application\Model\Factory\AuthModelFactory;
use Application\Model\Factory\AuthServiceFactory;
use Application\Model\Factory\SalabModelFactory;
use Application\Model\Factory\SessionModelFactory;
use Application\Model\SalabModel;
use Application\Model\SessionModel;
use Application\View\Helper\Breadcrumbs;
use Application\View\Helper\Menu;
use Application\View\Helper\Messenger;
use Application\View\Helper\PageTitle;
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/login[/:action]',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            
            // ADM.
            'administrador' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/administrador[/:action]',
                    'defaults' => [
                        'controller' => AdministradorController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            
            'cadastrar-usuario' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cadastrar-usuario[/:action]',
                    'defaults' => [
                        'controller' => AdministradorController::class,
                        'action'     => 'cadastrar-usuario',
                    ],
                ],
            ],
            
            'cadastrar-laboratorio' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cadastrar-laboratorio[/:action]',
                    'defaults' => [
                        'controller' => AdministradorController::class,
                        'action'     => 'cadastrar-laboratorio',
                    ],
                ],
            ],
            
            // ----------------------------------------------------------------
            'professor' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/professor[/:action]',
                    'defaults' => [
                        'controller' => ProfessorController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'laboratorista' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/laboratorista[/:action]',
                    'defaults' => [
                        'controller' => LaboratoristaController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AuthController::class          => AuthControllerFactory::class,
            AdministradorController::class => AdministradorControllerFactory::class,
            ProfessorController::class     => ProfessorControllerFactory::class,
            LaboratoristaController::class => LaboratoristaControllerFactory::class,
        ],
        'aliases' => [
            'auth'  => AuthController::class,
            'adm'   => AdministradorController::class,
            'prof'  => ProfessorController::class,
            'labor' => LaboratoristaController::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            Menu::class        => InvokableFactory::class,
            PageTitle::class   => InvokableFactory::class,
            Breadcrumbs::class => InvokableFactory::class,
            Messenger::class   => InvokableFactory::class,
        ],
        'aliases' => [
            'menu'        => Menu::class,
            'pageTitle'   => PageTitle::class,
            'breadcrumbs' => Breadcrumbs::class,
            'messenger'   => Messenger::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Db::class                    => DbFactory::class,
            SalabModel::class            => SalabModelFactory::class,
            AuthModel::class             => AuthModelFactory::class,
            AuthAdapter::class           => AuthAdapterFactory::class,
            SessionModel::class          => SessionModelFactory::class,
            AuthenticationService::class => AuthServiceFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login'            => __DIR__ . '/../view/layout/login.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    
    'session_containers' => [
        'usuario',
    ],
    // Can be used to define the mode in which the filter functions ("restrictive" or "permissive").
    'access_filter' => [
        'options' => [
            'mode' => 'restrictive'
        ],
        // Lists the controllers and their actions, specifying the access type for each action.
        'controllers' => [
            AuthController::class => [
                ['actions' => '*', 'allow' => '*'],
            ],
            AdministradorController::class => [
                ['actions' => '*', 'allow' => '@']
            ],
            ProfessorController::class => [
                ['actions' => '*', 'allow' => '@']
            ],
            LaboratoristaController::class => [
                ['actions' => '*', 'allow' => '@']
            ],
        ]
    ],
];
