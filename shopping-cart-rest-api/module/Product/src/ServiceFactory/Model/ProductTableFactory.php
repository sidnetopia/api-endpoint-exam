<?php
namespace Product\ServiceFactory\Model;

use Product\Model\Product;
use Product\Model\ProductTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class ProductTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();
        $ResultSetPrototype->setArrayObjectPrototype(new Product());

        $TableGateway = new TableGateway(
            'products',
            $DbAdapter,
            null,
            $ResultSetPrototype
        );

        return new ProductTable($TableGateway);
    }
}