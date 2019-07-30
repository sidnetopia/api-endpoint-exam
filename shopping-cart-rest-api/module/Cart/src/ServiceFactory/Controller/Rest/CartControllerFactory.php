<?php
namespace Cart\ServiceFactory\Controller\Rest;

use Cart\Controller\Rest\CartController;
use Cart\Filter\CartItemFilter;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
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
        $CartItemFilter = $Container->get(CartItemFilter::class);

        return new CartController(
            $CartTable,
            $ProductTable,
            $CartItemTable,
            $hostname,
            $CartItemFilter
        );
    }
}