<?php
/**
 * ORDER PRECEDENCE FOR USING NAMESPACE
 * Controller->Model->Table->Service->Filter->Session
 * MODULES
 * Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart\Controller\Rest;


use Cart\Filter\CartItemFilter;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use mysql_xdevapi\Exception;
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
    private $CartItemFilter;

    public function __construct(
        CartTable $CartTable,
        CartItemTable $CartItemTable,
        $hostname,
        Product $Product,
        CartItemFilter $CartItemFilter
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->hostname  = $hostname;
        $this->Product   = $Product;
        $this->CartItemFilter   = $CartItemFilter;
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
        return new ApiProblemResponse(new ApiProblem(201, 'Created'));
        try {
            $cartId = $this->CartTable->fetchCart(['cart_id'])->current()->cart_id;
            $data['cart_id'] = $cartId;
            $this->CartItemFilter->setData($data);
            if (!$this->CartItemFilter->isValid()) {
                $error_messages = '';
                foreach($this->CartItemFilter->getMessages() as $key=>$value) {
                    $error_messages = $error_messages.$key;
                    foreach ($value as $messages) {
                        $error_messages = $error_messages." - ".$messages.",";
                    }
                }
                return new ApiProblemResponse(new ApiProblem(400, $error_messages));
            }
            $data = $this->CartItemFilter->getValues();
            $cartItem = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                    'cart_items.product_id' => $data['product_id'],
                    'cart_id' => $data['cart_id'],
                ], true,
                ['weight', 'price'])->current();

            if ($cartItem) {
                $cartItemData = array(
                    'weight' => new Expression("weight + ".$cartItem->weight*$data['qty']),
                    'qty' => new Expression("qty + {$data['qty']}"),
                    'price' => new Expression("price + ".$cartItem->price*$data['qty']),
                );
                $this->CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $cartItem->cart_item_id]);

                return new ApiProblemResponse(new ApiProblem(202, 'Updated'));
            } else {
                $this->CartItemTable->insertCartItem($data);

                return new ApiProblemResponse(new ApiProblem(201, 'Created'));
            }
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }
    }
}