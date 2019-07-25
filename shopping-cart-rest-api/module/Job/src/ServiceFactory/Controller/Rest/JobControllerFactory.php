<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Job\ServiceFactory\Controller\Rest;

use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Controller\Rest\JobController;
use Job\Model\JobItemsTable;
use Job\Model\JobOrderTable;
use Psr\Container\ContainerInterface;

class JobControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container      = $Container->getServiceLocator();
        $CartTable      = $Container->get(CartTable::class);
        $CartItemTable  = $Container->get(CartItemTable::class);
//        $ProductTable   = $Container->get(ProductTable::class);
        $JobOrderTable  = $Container->get(JobOrderTable::class);
        $JobItemsTable  = $Container->get(JobItemsTable::class);

//        $CustomerFilter = $Container->get(CustomerFilter::class);
//        $JobFilter      = $Container->get(JobFilter::class);
//        $Session        = $Container->get('\Job\Storage\JobSessionContainer');
        return new JobController(
            $JobOrderTable,
            $JobItemsTable,
            $CartTable,
            $CartItemTable
//            $ProductTable,

//            $JobItemsTable,
//            $CustomerFilter,
//            $JobFilter,
//            $Session
        );
    }
}