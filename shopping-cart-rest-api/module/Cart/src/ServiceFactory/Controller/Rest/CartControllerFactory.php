<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart\ServiceFactory\Controller\Rest;

use Cart\Controller\Rest\CartController;
use Cart\Filter\CartItemFilter;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Product\Model\Product;
use Product\Model\ProductTable;
use Psr\Container\ContainerInterface;

class CartControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container      = $Container->getServiceLocator();
        $CartTable      = $Container->get(CartTable::class);
        $ProductTable   = $Container->get(ProductTable::class);
        $CartItemTable  = $Container->get(CartItemTable::class);
        $hostname       = $Container->get('Config')['hostname'];
        $Product        = $Container->get(Product::class);
        $CartItemFilter = $Container->get(CartItemFilter::class);

        return new CartController(
            $CartTable,
            $ProductTable,
            $CartItemTable,
            $hostname,
            $Product,
            $CartItemFilter
        );
    }
}