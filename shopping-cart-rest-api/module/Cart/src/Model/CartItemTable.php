<?php
namespace Cart\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class CartItemTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function fetchCartItems($columns, $where, $joinToProducts = false, $productColumns = array())
    {
        $select = $this->TableGateway->getSql()->select();
        if ($columns) {
            $select->columns($columns);
        }

        if ($joinToProducts) {
            $select->join(
                array("p" => "products"),
                "p.product_id = cart_items.product_id",
                $productColumns,
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

    public function deleteCartItems($cart_id)
    {
        $this->TableGateway->delete(['cart_id' => $cart_id]);
    }

    public function insertOrUpdateCartItem($CartItem, $data, $weight, $unit_price)
    {
        $qty = $data['qty'];
        $totalWeight = $qty *$weight;
        $price = $qty * $unit_price;

        if ($CartItem) {
            $cartItemData = array(
                'weight' => new Expression("weight + {$totalWeight}"),
                'qty' => new Expression("qty + {$qty}"),
                'price' => new Expression("price + {$price}"),
            );

            $this->updateCartItem($cartItemData, ['cart_item_id' => $CartItem->cart_item_id]);

            $code = 202;
            $details = 'Item Updated!';
        } else {
            $data['weight'] = $totalWeight;
            $data['unit_price'] = $price;
            $data['price'] = $price;
            $this->insertCartItem($data);

            $code = 201;
            $details = 'Item Added!';
        }

        return [
            'response' => [
                'code' => $code,
                'details' => $details
            ],
            'cartItemTotals' => [
                'totalWeight' => $totalWeight,
                'subTotal' => $price
            ]];
    }
}