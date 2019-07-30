<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Shipping\ServiceFactory\Controller\Rest;

use Cart\Model\CartTable;
use Shipping\Controller\Rest\ShippingController;
use Shipping\Filter\ShippingFilter;
use Shipping\Service\ShippingService;
use Psr\Container\ContainerInterface;

class ShippingControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container = $Container->getServiceLocator();
        $CartTable = $Container->get(CartTable::class);
        $ShippingService = $Container->get(ShippingService::class);
        $ShippingFilter  = $Container->get(ShippingFilter::class);

        return new ShippingController(
            $CartTable,
            $ShippingService,
            $ShippingFilter
        );
    }
}