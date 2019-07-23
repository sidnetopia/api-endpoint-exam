<?php
namespace Application;

use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return array(
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => array(
        'factories' => array(
            Controller\IndexController::class => ServiceFactory\Controller\IndexControllerFactory::class,
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'ZF\ApiProblem\Listener\ApiProblemListener'             => 'ZF\ApiProblem\Factory\ApiProblemListenerFactory',
            'ZF\ApiProblem\Listener\RenderErrorListener'            => 'ZF\ApiProblem\Factory\RenderErrorListenerFactory',
            'ZF\ApiProblem\Listener\SendApiProblemResponseListener' => 'ZF\ApiProblem\Factory\SendApiProblemResponseListenerFactory',
            'ZF\ApiProblem\View\ApiProblemRenderer'                 => 'ZF\ApiProblem\Factory\ApiProblemRendererFactory',
            'ZF\ApiProblem\View\ApiProblemStrategy'                 => 'ZF\ApiProblem\Factory\ApiProblemStrategyFactory',
        ),

        'aliases'   => array(
            'ZF\ApiProblem\ApiProblemListener'  => 'ZF\ApiProblem\Listener\ApiProblemListener',
            'ZF\ApiProblem\RenderErrorListener' => 'ZF\ApiProblem\Listener\RenderErrorListener',
            'ZF\ApiProblem\ApiProblemRenderer'  => 'ZF\ApiProblem\View\ApiProblemRenderer',
            'ZF\ApiProblem\ApiProblemStrategy'  => 'ZF\ApiProblem\View\ApiProblemStrategy',
        ),
    ),
    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout'    => __DIR__ . '/../view/layout/layout.phtml',
            'base/index/index' => __DIR__ . '/../view/base/index/index.phtml',
            'error/404'        => __DIR__ . '/../view/error/404.phtml',
            'error/index'      => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'display_exceptions' => false
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);
