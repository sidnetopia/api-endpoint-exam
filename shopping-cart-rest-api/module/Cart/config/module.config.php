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
            'recipe' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/product[/:id]',
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
        'factories' => array(
            CartTable::class      => CartTableFactory::class,
            CartItemTable::class  => CartItemTableFactory::class,
            ProductTable::class   => ProductTableFactory::class,
            CustomerTable::class  => CustomerTableFactory::class,
            CartService::class    => CartServiceFactory::class,
            ProductService::class => ProductServiceFactory::class,
            CartFilter::class     => CartFilterFactory::class,
            CartItemFilter::class => CartItemFilterFactory::class,
            ProductFilter::class  => ProductFilterFactory::class,
            CustomerFilter::class => LoginFilterFactory::class,
            'Cart\Storage\CartSessionContainer' => CartSessionContainerFactory::class,
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'cart/cart/show-cart'   => __DIR__ . '/../view/cart/cart-page.phtml',
            'cart/cart/delete-cart-item' => __DIR__ . '/../view/cart/cart-page.phtml',
            'cart/cart/add-or-update-item-to-cart' => __DIR__ . '/../view/cart/cart-page.phtml',
            'cart/cart/update-cart-quantity'       => __DIR__ . '/../view/cart/cart-page.phtml',
        ),
    ),
);
