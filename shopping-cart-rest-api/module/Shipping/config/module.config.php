<?php
namespace Shipping;

use Shipping\Controller\Rest\ShippingController;
use Shipping\ServiceFactory\Controller\Rest\ShippingControllerFactory;
use Cart\Model\CartTable;
use Cart\ServiceFactory\Model\CartTableFactory;
use Shipping\Model\ShippingTable;
use Shipping\ServiceFactory\Model\ShippingTableFactory;
use Shipping\Service\ShippingService;
use Shipping\ServiceFactory\Service\ShippingServiceFactory;

return [
    'router' => [
        'routes' => [
            'shipping' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/shipping[/:id]',
                    'defaults' => [
                        'controller' => ShippingController::class
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            ShippingController::class => ShippingControllerFactory::class
        ]
    ],
    'service_manager' => [
        'invokables' => [
          Filter\ShippingFilter::class => Filter\ShippingFilter::class
        ],
        'factories' => [
            CartTable::class => CartTableFactory::class,
            ShippingTable::class => ShippingTableFactory::class,
            ShippingService::class => ShippingServiceFactory::class
        ]
    ],
    'view_manager' => [
        'template_map' => [
            'shipping/shipping/show-shipping' => __DIR__ . '/../view/shipping/shipping-page.phtml',
            'shipping/shipping/add-shipping-to-cart' => __DIR__ . '/../view/shipping/shipping-page.phtml'
        ]
    ]
];
