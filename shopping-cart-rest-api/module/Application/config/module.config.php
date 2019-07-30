<?php
namespace Application;

use Zend\Mvc\Router\Http\Literal;
use Application\Controller\IndexController;
use Application\ServiceFactory\Controller\IndexControllerFactory;

return [
    'hostname' => "http://localhost/",
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
        ]
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'ZF\ApiProblem\Listener\ApiProblemListener'             => 'ZF\ApiProblem\Factory\ApiProblemListenerFactory',
            'ZF\ApiProblem\Listener\RenderErrorListener'            => 'ZF\ApiProblem\Factory\RenderErrorListenerFactory',
            'ZF\ApiProblem\Listener\SendApiProblemResponseListener' => 'ZF\ApiProblem\Factory\SendApiProblemResponseListenerFactory',
            'ZF\ApiProblem\View\ApiProblemRenderer'                 => 'ZF\ApiProblem\Factory\ApiProblemRendererFactory',
            'ZF\ApiProblem\View\ApiProblemStrategy'                 => 'ZF\ApiProblem\Factory\ApiProblemStrategyFactory',
        ],

        'aliases'   => [
            'ZF\ApiProblem\ApiProblemListener'  => 'ZF\ApiProblem\Listener\ApiProblemListener',
            'ZF\ApiProblem\RenderErrorListener' => 'ZF\ApiProblem\Listener\RenderErrorListener',
            'ZF\ApiProblem\ApiProblemRenderer'  => 'ZF\ApiProblem\View\ApiProblemRenderer',
            'ZF\ApiProblem\ApiProblemStrategy'  => 'ZF\ApiProblem\View\ApiProblemStrategy',
        ]
    ],
    
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout'    => __DIR__ . '/../view/layout/layout.phtml',
            'base/index/index' => __DIR__ . '/../view/base/index/index.phtml',
            'error/404'        => __DIR__ . '/../view/error/404.phtml',
            'error/index'      => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
            'display_exceptions' => false
        ]
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [],
        ],
    ]
];
