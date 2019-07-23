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

    public function fetchCart($cart_id)
    {
        $cart_id = (int) $cart_id;
        $select = $this->TableGateway->getSql()->select();
        $select->where(array("cart_id" => $cart_id));
        $Cart = $this->TableGateway->selectWith($select)->current();
        return $Cart;
    }

    public function fetchCartWithColumns($cart_id, $columns)
    {
        $select = $this->TableGateway->getSql()->select();
        $select->columns($columns)->where(array('cart_id' => $cart_id));
        $Cart = $this->TableGateway->selectWith($select)->current();
        return $Cart;
    }

    public function updateCart($cart_id, $data)
    {
        $where = array('cart_id' => $cart_id);
        $this->TableGateway->update($data, $where);
    }

    public function insertCart($data)
    {
        $this->TableGateway->insert($data);
        return $this->TableGateway->getLastInsertValue();
    }

    public function deleteCart($cart_id)
    {
        $deleteFlag = $this->TableGateway->delete(array('cart_id' => $cart_id));
        return $deleteFlag;
    }

}