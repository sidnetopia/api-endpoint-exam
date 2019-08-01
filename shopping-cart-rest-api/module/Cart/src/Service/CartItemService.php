<?php
namespace Cart\Service;

use Cart\Model\CartItemTable;
use Zend\Db\Sql\Expression;

class CartItemService
{
    public function insertOrUpdateCartItem(CartItemTable $CartItemTable , $Product, $data /*naming*/, $cartItemId = null)
    { ///update cart totals
        $qty = $data['qty'];
        $totalWeight = $qty * $Product->weight;
        $price = $qty * $Product->price;

        if ($cartItemId) {
            $cartItemData = array(
                'weight' => new Expression("weight + {$totalWeight}"),
                'qty' => new Expression("qty + {$qty}"),
                'price' => new Expression("price + {$price}"),
            );

            $CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $cartItemId]);

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
            'response' => [ // naming
                'code' => $code,
                'details' => $details
            ],
            'cartItemTotals' => [
                'totalWeight' => $totalWeight,
                'subTotal' => $price
            ]];
    }
}