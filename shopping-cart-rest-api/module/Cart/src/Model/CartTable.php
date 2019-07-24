<?php
namespace Cart\Model;

use Zend\Db\TableGateway\TableGateway;

class CartTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function fetchCart($columns = null)
    {

        $select = $this->TableGateway->getSql()->select();
        if($columns){
            $select->columns($columns);
        }
        $select->order('cart_id DESC');
        $Cart = $this->TableGateway->selectWith($select);

        return $Cart;
    }
}