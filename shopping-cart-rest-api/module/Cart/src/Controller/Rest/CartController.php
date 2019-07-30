<?php
namespace Cart\Controller\Rest;

use Cart\Filter\CartItemFilter;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Product\Model\ProductTable;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class CartController extends AbstractRestfulController
{
    private $CartTable;
    private $ProductTable;
    private $CartItemTable;
    private $hostname;
    private $CartItemFilter;

    public function __construct(
        CartTable $CartTable,
        ProductTable $ProductTable,
        CartItemTable $CartItemTable,
        $hostname,
        CartItemFilter $CartItemFilter
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->ProductTable = $ProductTable;
        $this->hostname  = $hostname;
        $this->CartItemFilter   = $CartItemFilter;
    }

    /**
     * Get cart items and cart details
     *
     * @return mixed|JsonModel|ApiProblemResponse
     */
    public function getList()
    {
        try {
            $Cart = $this->CartTable->fetchCart(
                ['cart_id', 'sub_total', 'shipping_total', 'total_amount'])->current();
            $CartItems = $this->CartItemTable->fetchCartItems(['qty', 'item_price' => 'price'],
                ['cart_id' => $Cart->cart_id], true,['product_thumbnail', 'price', 'product_desc']);

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }


        $cartItemArray = array();
        foreach ($CartItems as $key => $value) {
            $cartItemArray[$key] = $value;
            $cartItemArray[$key]['product_thumbnail'] =trim($value['product_thumbnail'], $this->hostname);
        }

        return new JsonModel(['cartItems' => $cartItemArray, 'cartDetails' => get_object_vars($Cart)]);
    }

    /**
     * Create or update cart item, then update cart
     *
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
        public function create($data)
    {
        try {
            $cartId = $this->CartTable->fetchCart(['cart_id'])->current()->cart_id;
            $this->CartItemFilter->setData($data);

            if (!$this->CartItemFilter->isValid()) {
                $error_messages = $this->CartItemFilter->getErrorMessage();
                return new ApiProblemResponse(new ApiProblem(400, $error_messages));
            }

            $data = $this->CartItemFilter->getValues();
            $data['cart_id'] = $cartId;
            $CartItem = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                'cart_items.product_id' => $data['product_id'],
                'cart_id' => $cartId,
            ], true,
                ['weight', 'price'])->current();

            $returnDetails = $this->createOrUpdateCartItem($CartItem, $data);
            $cartItemDetails = $returnDetails['cartItemDetails'];
            $subTotal = $cartItemDetails['subTotal'];

            $cartData = array(
                'total_weight' => new Expression("total_weight + {$cartItemDetails['totalWeight']}"),
                'sub_total' => new Expression("sub_total + {$subTotal}"),
                'total_amount' => new Expression("total_amount + {$subTotal}"),
            );

            $this->CartTable->updateCart($cartData, ['cart_id' => $cartId]);

            $response = $returnDetails['response'];
            return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    /**
     * Create or update cart items by qty and product id.
     *
     * @param $CartItem
     * @param $data
     * @return array
     */
    private function createOrUpdateCartItem($CartItem, $data)
    {
        $qty = $data['qty'];

        if ($CartItem) {
            $subTotal = $CartItem->price*$qty;
            $totalWeight = $CartItem->weight*$qty;

            $cartItemData = array(
                'weight' => new Expression("weight + {$totalWeight}"),
                'qty' => new Expression("qty + {$qty}"),
                'price' => new Expression("price + {$subTotal}"),
            );

            $this->CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $CartItem->cart_item_id]);

            $code = 202;
            $details = 'Item Updated!';
        } else {
            $Product = $this->ProductTable->fetchProducts(['weight', 'price'],
                                                        ['product_id' => $data['product_id']])->current();

            $totalWeight = $Product->weight;
            $subTotal = $Product->price;
            $data['weight'] = $qty * $Product->weight;
            $data['unit_price'] = $Product->price;
            $data['price'] = $qty * $Product->price;
            $this->CartItemTable->insertCartItem($data);

            $code = 201;
            $details = 'Item Added!';
        }

        return [
            'response' => [
                'code' => $code,
                'details' => $details
            ],
            'cartItemDetails' => [
                'totalWeight' => $totalWeight,
                'subTotal' => $subTotal
            ]];
    }

    public function options()
    {
    }
}