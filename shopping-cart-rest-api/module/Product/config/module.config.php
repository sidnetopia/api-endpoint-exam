<?php
namespace Product;

return array(
    'hostname' => "http://localhost/",
    'router' => array(
        'routes' => array(
        'recipe' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/product[/:id]',
                'defaults' => array(
                    'controller' => Controller\Rest\ProductController::class,
                ),
            ),
        ),
    ),
),
    'controllers' => array(
        'factories' => array(
            Controller\Rest\ProductController::class => ServiceFactory\Controller\Rest\ProductControllerFactory::class
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            Filter\ProductFilter::class => Filter\ProductFilter::class,
            Model\Product::class => Model\Product::class,
        ),
        'factories' => array(
            Model\ProductTable::class => ServiceFactory\Model\ProductTableFactory::class,
        ),
    ),
);
