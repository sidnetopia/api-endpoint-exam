<?php
namespace Shipping\ServiceFactory\Service;

use Psr\Container\ContainerInterface;
use Shipping\Model\ShippingTable;
use Shipping\Service\ShippingService;

class ShippingServiceFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $ShippingTable = $Container->get(ShippingTable::class);

        return new ShippingService($ShippingTable);
    }
}