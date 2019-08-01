<?php
namespace Job;

use Job\Controller\Rest\JobController;
use Job\Service\JobItemService;
use Job\ServiceFactory\Controller\Rest\JobControllerFactory;
use Cart\Model\CartTable;
use Cart\ServiceFactory\Model\CartTableFactory;
use Product\Model\ProductTable;
use Product\ServiceFactory\Model\ProductTableFactory;
use Job\Model\JobOrderTable;
use Job\ServiceFactory\Model\JobOrderTableFactory;
use Job\Model\JobItemsTable;
use Job\ServiceFactory\Model\JobItemsTableFactory;

return [
    'router' => [
        'routes' => [
            'job' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/job[/:id]',
                    'defaults' => [
                        'controller' => JobController::class,
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            JobController::class => JobControllerFactory::class
        ]
    ],
    'service_manager' => [
        'factories' => [
            CartTable::class => CartTableFactory::class,
            ProductTable::class => ProductTableFactory::class,
            JobOrderTable::class  => JobOrderTableFactory::class,
            JobItemsTable::class  => JobItemsTableFactory::class
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'job/job/show-order-confirmation' => __DIR__ . '/../view/job/order-confirmation-page.phtml'
        ]
    ]
];
