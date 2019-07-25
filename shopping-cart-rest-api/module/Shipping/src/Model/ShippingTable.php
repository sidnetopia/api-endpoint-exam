<?php
namespace Shipping\Model;

use Zend\Db\TableGateway\TableGateway;

class ShippingTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function fetchShipping()
    {
        $select = $this->TableGateway->getSql()->select()->columns([
            'min_weight',
            'max_weight',
            'shipping_method',
            'shipping_rate'
            ]);

        return $this->TableGateway->selectWith($select);
    }
}