<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Job;

use Job\Controller\JobController;
use Job\Model\JobOrderTable;
use Job\Model\JobItemsTable;
use Product\Model\ProductTable;
use Customer\Filter\CustomerFilter;
use Job\Filter\JobFilter;
use Job\ServiceFactory\Controller\JobControllerFactory;
use Product\ServiceFactory\Model\ProductTableFactory;
use Job\ServiceFactory\Model\JobOrderTableFactory;
use Job\ServiceFactory\Model\JobItemsTableFactory;
use Customer\ServiceFactory\Filter\CustomerFilterFactory;
use Job\ServiceFactory\Filter\JobFilterFactory;
use Job\ServiceFactory\Storage\JobSessionContainerFactory;
use Zend\Mvc\Router\Http\Segment;

return array(
    'router' => array(
        'routes' => array(
            'job' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/job[/:id]',
                    'defaults' => array(
                        'controller' => Controller\Rest\JobController::class,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            Controller\Rest\JobController::class => ServiceFactory\Controller\Rest\JobControllerFactory::class,
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            Cart\Model\CartTable::class => Cart\ServiceFactory\Model\CartTableFactory::class,
            Product\Model\ProductTable::class => Product\ServiceFactory\Model\ProductTableFactory::class,
            Model\JobOrderTable::class  => ServiceFactory\Model\JobOrderTableFactory::class,
            Model\JobItemsTable::class  => ServiceFactory\Model\JobItemsTableFactory::class
        ),
        'invokables' => array(
            Model\JobOrder::class => Model\JobOrder::class,
            Product\Model\Product::class => Product\Model\Product::class,
        ),
//        'factories' => array(
//            ProductTable::class   => ProductTableFactory::class,
//            JobOrderTable::class  => JobOrderTableFactory::class,
//            JobItemsTable::class  => JobItemsTableFactory::class,
//            CustomerFilter::class => CustomerFilterFactory::class,
//            JobFilter::class      => JobFilterFactory::class,
//            'Job\Storage\JobSessionContainer' => JobSessionContainerFactory::class,
//        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'job/job/show-order-confirmation' => __DIR__ . '/../view/job/order-confirmation-page.phtml',
        ),
    )

);
