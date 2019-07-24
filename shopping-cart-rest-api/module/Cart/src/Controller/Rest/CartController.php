<?php
/**
 * ORDER PRECEDENCE FOR USING NAMESPACE
 * Controller->Model->Table->Service->Filter->Session
 * MODULES
 * Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart\Controller\Rest;


use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Product\Model\Product;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class CartController extends AbstractRestfulController
{
    private $CartTable;
    private $CartItemTable;
    private $hostname;
    private $Product;

    public function __construct(
        CartTable $CartTable,
        CartItemTable $CartItemTable,
        $hostname,
        Product $Product
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->hostname  = $hostname;
        $this->Product   = $Product;
    }

    public function getList()
    {
        try {
            $Cart = $this->CartTable->fetchCart(
                ['cart_id', 'sub_total', 'shipping_total', 'total_amount'])->current();
            $CartItems = $this->CartItemTable->fetchCartItems(['qty', 'price'],
                ['cart_id' => $Cart->cart_id], true);

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }

        $cartItemArray = array();
        foreach ($CartItems as $key => $value) {
            $cartItemArray[$key] = $value;
            $cartItemArray[$key]['product_thumbnail'] = $this->Product
                ->getImagePath($value['product_thumbnail'], $this->hostname);
        }

        
        return new JsonModel(['cartItems' => $cartItemArray, 'cartDetails' => $Cart]);
    }

    public function create($data)
    {
        try {
            $cartItemId = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                    'product_id' => $data['product_id'],
                    'cart_id' => $data['cart_id'],
                ], false)->current()->cart_item_id;
            if ($cartItemId) {
                $cartItemData = array(
                    'weight' => new Expression("weight + {$data['weight']}"),
                    'qty' => new Expression("qty + {$data['qty']}"),
                    'price' => new Expression("price + {$data['price']}"),
                );
                $this->CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $cartItemId]);
                return new ApiProblemResponse(new ApiProblem(201, 'Created'));
            } else {
                $this->CartItemTable->insertCartItem($data);
                return new ApiProblemResponse(new ApiProblem(202, 'Updated'));
            }
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }
    }
}