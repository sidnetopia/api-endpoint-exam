<?php
namespace Job\ServiceFactory\Model;

use Job\Model\JobItems;
use Job\Model\JobItemsTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class JobItemsTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        // Creation for table gateway instance
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();

        // create TableGateway instance
        $TableGateway = new TableGateway(
            'job_items',
            $DbAdapter,
            null,
            $ResultSetPrototype
        );
        return new JobItemsTable($TableGateway);
    }
}