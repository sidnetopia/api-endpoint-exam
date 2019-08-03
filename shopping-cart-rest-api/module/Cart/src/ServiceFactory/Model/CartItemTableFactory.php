<?php
namespace Cart\ServiceFactory\Model;

use Cart\Model\CartItem;
use Cart\Model\CartItemTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class CartItemTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        // Creation for table gateway instance
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();
        $ResultSetPrototype->setArrayObjectPrototype(new CartItem());

        // create TableGateway instance
        $TableGateway = new TableGateway(
            'cart_items',
            $DbAdapter,
            null,
            $ResultSetPrototype
        );

        return new CartItemTable($TableGateway);
    }
}