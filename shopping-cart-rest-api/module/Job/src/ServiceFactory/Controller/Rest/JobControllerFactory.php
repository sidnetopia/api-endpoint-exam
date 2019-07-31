<?php
namespace Job\ServiceFactory\Controller\Rest;

use Application\Service\CoreService;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Controller\Rest\JobController;
use Job\Model\JobItemsTable;
use Job\Model\JobOrderTable;
use Product\Model\Product;
use Psr\Container\ContainerInterface;

class JobControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container      = $Container->getServiceLocator();
        $CartTable      = $Container->get(CartTable::class);
        $CartItemTable  = $Container->get(CartItemTable::class);
        $JobOrderTable  = $Container->get(JobOrderTable::class);
        $JobItemsTable  = $Container->get(JobItemsTable::class);
        $hostname       = $Container->get('Config')['hostname'];
        $CoreService    = $Container->get(CoreService::class);
        $Product        = $Container->get(Product::class);

        return new JobController(
            $JobOrderTable,
            $JobItemsTable,
            $CartTable,
            $CartItemTable,
            $hostname,
            $CoreService,
            $Product
        );
    }
}