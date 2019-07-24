<?php
namespace Cart\ServiceFactory\Model;

use Cart\Model\Cart;
use Cart\Model\CartTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class CartTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        // Creation for table gateway instance
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();

        // create TableGateway instance
        $TableGateway = new TableGateway(
            ['c' => 'carts'],
            $DbAdapter,
            null,
            $ResultSetPrototype
        );

        return new CartTable($TableGateway);
    }
}