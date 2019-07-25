<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Shipping;



return array(
    'router' => array(
        'routes' => array(
            'shipping' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/shipping[/:id]',
                    'defaults' => array(
                        'controller' => Controller\Rest\ShippingController::class,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            Controller\Rest\ShippingController::class => ServiceFactory\Controller\Rest\ShippingControllerFactory::class
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
          Filter\ShippingFilter::class => Filter\ShippingFilter::class
        ),
        'factories' => array(
            Cart\Model\CartTable::class => Cart\ServiceFactory\Model\CartTableFactory::class,
            Model\ShippingTable::class => ServiceFactory\Model\ShippingTableFactory::class,
//            CartService::class   => CartServiceFactory::class,
            Service\ShippingService::class => ServiceFactory\Service\ShippingServiceFactory::class,
//            CartFilter::class      => CartFilterFactory::class,
//            CustomerFilter::class  => CustomerFilterFactory::class,
//            ShippingFilter::class  => ShippingFilterFactory::class,
//            'Shipping\Storage\ShippingSessionContainer' => ShippingSessionContainerFactory::class,
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'shipping/shipping/show-shipping' => __DIR__ . '/../view/shipping/shipping-page.phtml',
            'shipping/shipping/add-shipping-to-cart' => __DIR__ . '/../view/shipping/shipping-page.phtml',
        ),
    )
);
