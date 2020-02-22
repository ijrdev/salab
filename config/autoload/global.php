<?php

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;

//error_reporting(E_ALL);
//ini_set('display_errors', true);

return [
    'connections' => [
        'salab' => [
            'dsn'            => 'mysql:host=157.245.123.80;port=3306;dbname=salab;',
            'username'       => 'salab',
            'password'       => 'projetosalab',
            'driver'         => 'Pdo',
            'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''],
        ]
    ],
    
    'smtp_accounts' => [
        'gmail' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'connection_class'  => 'login',
            'connection_config' => [
                'username' => 'projetosalab2020@gmail.com',
                'password' => 'salab2020',
                'ssl'      => 'tls'
            ],
        ]
    ],
        
    // Session configuration.
    'session_config' => [
        // Session cookie will expire in 1 hour.
        'cookie_lifetime' => 60*60*1,     
        // Session data will be stored on server maximum for 30 days.
        'gc_maxlifetime' => 60*60*24*30, 
    ],
    
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];