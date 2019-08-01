<?php
namespace Cart\Service;

use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Zend\Db\Sql\Expression;

class CartService
{
    public function deleteAndCreateCart(CartItemTable $CartItemTable, CartTable $CartTable, $cartId)
    {
        try{ //
            $CartItemTable->deleteCartItems($cartId);
            $CartTable->deleteCart($cartId);

            $data = array(
                'order_datetime' => new Expression("NOW()"),
            );

            $CartTable->insertCart($data);

            $code = 202;
            $details = 'Accepted';

        } catch (\Exception $e) {
            $code = 500;
            $details = 'Internal Server Error';
        }

        return ['code' => $code, 'details' => $details];
    }
}