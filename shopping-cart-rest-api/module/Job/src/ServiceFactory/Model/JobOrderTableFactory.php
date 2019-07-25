<?php
namespace Job\ServiceFactory\Model;

use Job\Model\JobOrder;
use Job\Model\JobOrderTable;
use Psr\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class JobOrderTableFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        // Creation for table gateway instance
        $DbAdapter = $Container->get('shopping_cart');
        $ResultSetPrototype = new ResultSet();
        $ResultSetPrototype->setArrayObjectPrototype(new JobOrder());

        // create TableGateway instance
        $TableGateway = new TableGateway(
            'job_orders',
            $DbAdapter,
            null,
            $ResultSetPrototype
        );
        return new JobOrderTable($TableGateway);
    }
}