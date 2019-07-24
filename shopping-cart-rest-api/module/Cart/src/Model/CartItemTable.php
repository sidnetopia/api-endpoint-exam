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

    public function fetchCartItems($columns, $where, $joinToProducts = false)
    {
        $select = $this->TableGateway->getSql()->select();
        if ($columns) {
            $select->columns($columns);
        }

        if ($joinToProducts) {
            $select->join(
                array("p" => "products"),
                "p.product_id = ci.product_id",
                array('product_thumbnail', 'product_name', 'product_desc', 'price'),
                "INNER"
            );
        }

        if ($where) {
            $select->where($where);
        }
        $CartItems = $this->TableGateway->selectWith($select);

        return $CartItems;
    }

    public function insertCartItem($data)
    {
        $this->TableGateway->insert($data);
    }

    public function updateCartItem($data, $where)
    {
        $update = $this->TableGateway->getSql()->update()->set($data)->where($where);
        $this->TableGateway->updateWith($update);
    }
}