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

    public function fetchCartItems($cart_id)
    {
        $select = $this->TableGateway->getSql()->select()
            ->columns(['qty', 'price'])
            ->join(
            array("p" => "products"),
            "p.product_id = ci.product_id",
            array('product_thumbnail', 'product_name', 'product_desc', 'price'),
            "INNER"
             )->where(['cart_id' => $cart_id]);

        $CartItems = $this->TableGateway->selectWith($select);

        return $CartItems;
    }
}