<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart\ServiceFactory\Controller\Rest;

use Cart\Controller\CartController;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Product\Model\ProductTable;
use Customer\Model\CustomerTable;
use Cart\Service\CartService;
use Product\Service\ProductService;
use Cart\Filter\CartFilter;
use Cart\Filter\CartItemFilter;
use Product\Filter\ProductFilter;
use Customer\Filter\CustomerFilter;
use Psr\Container\ContainerInterface;

class CartControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container      = $Container->getServiceLocator();
        $CartTable      = $Container->get(CartTable::class);
        $CartItemTable  = $Container->get(CartItemTable::class);
        $ProductTable   = $Container->get(ProductTable::class);
        $CustomerTable  = $Container->get(CustomerTable::class);
        $CartService    = $Container->get(CartService::class);
        $ProductService = $Container->get(ProductService::class);
        $CartFilter     = $Container->get(CartFilter::class);
        $CartItemFilter = $Container->get(CartItemFilter::class);
        $ProductFilter  =  $Container->get(ProductFilter::class);
        $CustomerFilter = $Container->get(CustomerFilter::class);
        $Session        = $Container->get('Cart\Storage\CartSessionContainer');

        return new CartController(
            $CartTable,
            $CartItemTable,
            $ProductTable,
            $CustomerTable,
            $CartService,
            $ProductService,
            $CartFilter,
            $CartItemFilter,
            $ProductFilter,
            $CustomerFilter,
            $Session
        );
    }
}