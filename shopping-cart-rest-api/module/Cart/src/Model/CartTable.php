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

    public function fetchCart()
    {
//        SELECT product_id FROM products WHERE product_id = (SELECT MAX( product_id ) FROM products )
        $select = $this->TableGateway->getSql()->select()->columns(['cart_id', 'sub_total', 'shipping_total', 'total_amount'])->order('cart_id DESC');
//            array(
//            "cart_id" => new Expression('MAX( cart_id )'),
//            "sub_total",
//            "shipping_total",
//            "total_amount",
//            ))->join(
//            array("ci" => "cart_items"),
//            "ci.cart_id = c.cart_id",
//            array('qty', 'price'),
//            "INNER"
//            )->join(
//            array("p" => "products"),
//            "p.product_id = ci.product_id",
//            array('product_thumbnail', 'product_name', 'product_desc', 'price'),
//            "INNER"
//             );
//        $query = 'SELECT c.sub_total AS sub_total, c.shipping_total AS shipping_total,
//                    c.total_amount AS total_amount, ci.qty AS qty, ci.price AS price,
//                    p.product_thumbnail AS product_thumbnail, p.product_name AS product_name,
//                    p.product_desc AS product_desc, p.price AS cart_item_price FROM carts AS c INNER JOIN
//                    cart_items AS ci ON ci.cart_id = c.cart_id INNER JOIN products AS p ON
//                    p.product_id = ci.product_id WHERE c.cart_id = (SELECT MAX(cart_id) FROM carts)';
//        $query = 'SELECT cart_id WHERE cart_id = (SELECT MAX( cart_id ) FROM carts )';
//
//        $Cart = $this->TableGateway->getAdapter()->driver->getConnection()->execute($query);
        $Cart = $this->TableGateway->selectWith($select);

        return $Cart;
    }

}