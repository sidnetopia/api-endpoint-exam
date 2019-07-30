<?php
namespace Product;

use Product\Controller\Rest\ProductController;
use Product\ServiceFactory\Controller\Rest\ProductControllerFactory;
use Product\Filter\ProductFilter;
use Product\Model\ProductTable;
use Product\ServiceFactory\Model\ProductTableFactory;

return [
    'router' => [
        'routes' => [
            'product' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/product[/:id]',
                    'defaults' => [
                        'controller' => ProductController::class
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            ProductController::class => ProductControllerFactory::class
        ]
    ],
    'service_manager' => [
        'invokables' => [
            ProductFilter::class => ProductFilter::class
        ],
        'factories' => [
            ProductTable::class => ProductTableFactory::class
        ]
    ]
];
