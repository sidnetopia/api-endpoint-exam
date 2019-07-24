<?php
/**
 * ORDER PRECEDENCE FOR USING NAMESPACE
 * Controller->Model->Table->Service->Filter->Session
 * MODULES
 * Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart;

return array(
    'router' => array(
        'routes' => array(
            'cart' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/cart[/:id]',
                    'defaults' => array(
                        'controller' => Controller\Rest\CartController::class,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            Controller\Rest\CartController::class => ServiceFactory\Controller\Rest\CartControllerFactory::class
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            Model\Cart::class => Model\Cart::class,
            Product\Model\Product::class => Product\Model\Product::class,
        ),
        'factories' => array(
            Model\CartTable::class => ServiceFactory\Model\CartTableFactory::class,
            Model\CartItemTable::class => ServiceFactory\Model\CartItemTableFactory::class,
        ),
    ),
);
