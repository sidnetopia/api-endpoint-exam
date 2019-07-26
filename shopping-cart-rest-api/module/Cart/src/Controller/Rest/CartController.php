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
    private $Product;
    private $CartItemFilter;

    public function __construct(
        CartTable $CartTable,
        ProductTable $ProductTable,
        CartItemTable $CartItemTable,
        $hostname,
        Product $Product,
        CartItemFilter $CartItemFilter
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->ProductTable = $ProductTable;
        $this->hostname  = $hostname;
        $this->Product   = $Product;
        $this->CartItemFilter   = $CartItemFilter;
    }

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
            $cartItemArray[$key]['product_thumbnail'] = $this->Product
                ->getImagePath($value['product_thumbnail'], $this->hostname);
        }

        return new JsonModel(['cartItems' => $cartItemArray, 'cartDetails' => get_object_vars($Cart)]);
    }

    public function create($data)
    {
        $subTotal = 0;
        $totalWeight = 0;

        try {
            $cartId = $this->CartTable->fetchCart(['cart_id'])->current()->cart_id;
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
            $data['cart_id'] = $cartId;
            $productId = $data['product_id'];
            $cartItem = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                    'cart_items.product_id' => $productId,
                    'cart_id' => $data['cart_id'],
                ], true,
                ['weight', 'price'])->current();

            if ($cartItem) {
                $subTotal = $cartItem->price*$data['qty'];
                $totalWeight = $cartItem->weight*$data['qty'];

                $cartItemData = array(
                    'weight' => new Expression("weight + ".$totalWeight),
                    'qty' => new Expression("qty + {$data['qty']}"),
                    'price' => new Expression("price + ".$subTotal),
                );

                $this->CartItemTable->updateCartItem($cartItemData, ['cart_item_id' => $cartItem->cart_item_id]);

                $code = 202;
                $details = 'Item Updated!';
            } else {
                $Product = $this->ProductTable->fetchProduct($productId);
                $totalWeight = $Product->weight;
                $subTotal = $Product->price;
                $data['weight'] = $Product->weight;
                $data['unit_price'] = $Product->price;
                $data['price'] = $Product->price;
                $this->CartItemTable->insertCartItem($data);

                $code = 201;
                $details = 'Item Added!';
            }

            $cartData = array(
                'total_weight' => new Expression("total_weight + ".$totalWeight),
                'sub_total' => new Expression("sub_total + ".$subTotal),
                'total_amount' => new Expression("total_amount + ".$subTotal),
            );

            $this->CartTable->updateCart($cartData, ['cart_id' => $cartId]);

            return new ApiProblemResponse(new ApiProblem($code, $details));

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    public function options()
    {
        return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error' ));
    }
}