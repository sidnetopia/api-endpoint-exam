<?php
namespace Product;

use Application\Service\CoreService;
use Product\Controller\Rest\ProductController;
use Product\Model\Product;
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
            ProductFilter::class => ProductFilter::class,
            CoreService::class => CoreService::class,
            Product::class => Product::class
        ],
        'factories' => [
            ProductTable::class => ProductTableFactory::class
        ]
    ]
];
