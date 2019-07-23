<?php
namespace Cart\Model;

use Zend\Db\TableGateway\TableGateway;

class CartItemTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function fetchCartItemByCartAndProductID($cart_id, $product_id = 0)
    {
        $select = $this->TableGateway->getSql()->select()
            ->where(array('cart_id' => $cart_id))
            ->where(array('product_id' => $product_id));
        $CartItem = $this->TableGateway->selectWith($select)->current();
        return $CartItem;
    }

    public function fetchCartItemByCartID($cart_id, $columns)
    {
        $select = $this->TableGateway->getSql()->select()
            ->columns($columns)->where(array('cart_id' => $cart_id));
        $CartItem = $this->TableGateway->selectWith($select);
        return $CartItem;
    }

    public function fetchCartItemGroupByCartID($cart_id, $columns)
    {
        $select = $this->TableGateway->getSql()->select()
            ->columns($columns)->where(array('cart_id' => $cart_id))->group(array('cart_id'));
        $CartItem = $this->TableGateway->selectWith($select);
        return $CartItem;
    }

    public function fetchCartItemWithColumns($cart_item_id, $columns)
    {
        $select = $this->TableGateway->getSql()->select();
        $select->columns($columns)->where(array('cart_item_id' => $cart_item_id));
        $ResultSet = $this->TableGateway->selectWith($select)->current();
        return $ResultSet;
    }

    public function updateCartItem($cart_item_id, $data)
    {
        $where = array('cart_item_id' => $cart_item_id);
        $this->TableGateway->update($data, $where);
    }

    public function insertCartItem($data)
    {
        $this->TableGateway->insert($data);
    }

    public function deleteCartItem($cart_item_id)
    {
        $deleteFlag = $this->TableGateway->delete(array('cart_item_id' => $cart_item_id));
        return $deleteFlag;
    }

    public function deleteCartItems($cart_id)
    {
        $deleteFlag = $this->TableGateway->delete(array('cart_id' => $cart_id));
        return $deleteFlag;
    }
}