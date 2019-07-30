<?php
namespace Cart;

use Cart\Controller\Rest\CartController;
use Cart\ServiceFactory\Controller\Rest\CartControllerFactory;
use Cart\Filter\CartItemFilter;
use Cart\Model\Cart;
use Product\Model\Product;
use Cart\Model\CartTable;
use Cart\ServiceFactory\Model\CartTableFactory;
use Cart\Model\CartItemTable;
use Cart\ServiceFactory\Model\CartItemTableFactory;
use Product\Model\ProductTable;
use Product\ServiceFactory\Model\ProductTableFactory;

return [
    'router' => [
        'routes' => [
            'cart' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/cart[/:id]',
                    'defaults' => [
                        'controller' => CartController::class,
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            CartController::class => CartControllerFactory::class
        ]
    ],
    'service_manager' => [
        'invokables' => [
            CartItemFilter::class => CartItemFilter::class,
            Cart::class => Cart::class,
            Product::class => Product::class
        ],
        'factories' => [
            CartTable::class => CartTableFactory::class,
            CartItemTable::class => CartItemTableFactory::class,
            ProductTable::class => ProductTableFactory::class
        ],
    ]
];
