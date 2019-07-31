<?php
namespace Product\ServiceFactory\Controller\Rest;

use Application\Service\CoreService;
use Product\Controller\Rest\ProductController;
use Product\Model\Product;
use Product\Model\ProductTable;
use Product\Filter\ProductFilter;
use Psr\Container\ContainerInterface;

class ProductControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container      =  $Container->getServiceLocator();
        $ProductTable   =  $Container->get(ProductTable::class);
        $ProductFilter  =  $Container->get(ProductFilter::class);
        $hostname       =  $Container->get('Config')['hostname'];
        $Product        =  $Container->get(Product::class);
        $CoreService    =  $Container->get(CoreService::class);

        return new ProductController(
            $ProductTable,
            $ProductFilter,
            $hostname,
            $Product,
            $CoreService
        );
    }
}