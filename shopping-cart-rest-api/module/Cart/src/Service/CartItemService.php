<?php
namespace Cart\Service;

use Cart\Model\CartItemTable;
use Zend\Db\Sql\Expression;

class CartItemService
{
    public function insertOrUpdateCartItem($CartItem, $Product, CartItemTable $CartItemTable, $data)
    {
        $qty = $data['qty'];
        $totalWeight = $qty * $Product->weight;
        $price = $qty * $Product->price;

        if ($CartItem) {
            $cartItemData = array(
                'weight' => new Expression("weight + {$totalWeight}"),
                'qty' => new Expression("qty + {$qty}"),
                'price' => new Expression("price + {$price}"),
            );

            $CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $CartItem->cart_item_id]);

            $code = 202;
            $details = 'Item Updated!';
        } else {
            $data['weight'] = $totalWeight;
            $data['unit_price'] = $price;
            $data['price'] = $price;
            $CartItemTable->insertCartItem($data);

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