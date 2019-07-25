<?php
namespace Shipping\ServiceFactory\Model;

use Shipping\Model\ShippingTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class ShippingTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        // Creation for table gateway instance
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();

        // create TableGateway instance
        $TableGateway = new TableGateway(
            'shipping',
            $DbAdapter,
            null,
            $ResultSetPrototype
        );
        return new ShippingTable($TableGateway);
    }
}