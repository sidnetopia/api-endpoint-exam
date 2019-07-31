<?php
namespace Cart\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class CartTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function insertCart($data)
    {
        $this->TableGateway->insert($data);
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

    public function updateCart($data, $where)
    {
        $update = $this->TableGateway->getSql()->update()->set($data)->where($where);
        $this->TableGateway->updateWith($update);
    }

    public function deleteCart($cart_id)
    {
        $this->TableGateway->delete(['cart_id' => $cart_id]);
    }

    public function fetchLatestCartId()
    {
        return $this->fetchCart(['cart_id'])->current()->cart_id;
    }

    public function updateCartTotals($totalWeight, $subTotal, $totalAmount, $cartId)
    {
        $cartData = array(
            'total_weight' => new Expression("total_weight + {$totalWeight}"),
            'sub_total' => new Expression("sub_total + {$subTotal}"),
            'total_amount' => new Expression("total_amount + {$totalAmount}"),
        );

        $this->updateCart($cartData, ['cart_id' => $cartId]);
    }
}