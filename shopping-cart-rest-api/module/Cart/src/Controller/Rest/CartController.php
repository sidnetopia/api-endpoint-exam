<?php
/**
 * ORDER PRECEDENCE FOR USING NAMESPACE
 * Controller->Model->Table->Service->Filter->Session
 * MODULES
 * Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Cart\Controller\Rest;

use Cart\Model\CartItem;
use Customer\Model\Customer;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Product\Model\ProductTable;
use Customer\Model\CustomerTable;
use Cart\Service\CartService;
use Product\Service\ProductService;
use Cart\Filter\CartFilter;
use Cart\Filter\CartItemFilter;
use Product\Filter\ProductFilter;
use Customer\Filter\CustomerFilter;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CartController extends AbstractActionController
{
    private $CartTable;
    private $CartItemTable;
    private $ProductTable;
    private $CustomerTable;
    private $CartService;
    private $ProductService;
    private $CartFilter;
    private $CartItemFilter;
    private $ProductFilter;
    private $CustomerFilter;
    private $Session;

    public function __construct(
        CartTable $cartTable,
        CartItemTable $cartItemTable,
        ProductTable $productTable,
        CustomerTable $customerTable,
        CartService $cartService,
        ProductService $productService,
        CartFilter $cartFilter,
        CartItemFilter $cartItemFilter,
        ProductFilter $productFilter,
        CustomerFilter $customerFilter,
        Container $session
    )
    {
        $this->CartTable      = $cartTable;
        $this->CartItemTable  = $cartItemTable;
        $this->ProductTable   = $productTable;
        $this->CustomerTable  = $customerTable;
        $this->CartService    = $cartService;
        $this->ProductService = $productService;
        $this->CartFilter     = $cartFilter;
        $this->CartItemFilter = $cartItemFilter;
        $this->ProductFilter  = $productFilter;
        $this->CustomerFilter = $customerFilter;
        $this->Session        = $session;
    }

    private function createOrUpdateCart()
    {
        if ($this->Session->offsetExists('customer_id')) {
            $customer_id = $this->Session->offsetGet('customer_id');
            $this->CustomerFilter->setData(array('customer_id' => $customer_id));
            $this->CustomerFilter->setValidationGroup('customer_id');

            if (!$this->CustomerFilter->isValid()) {
                $ViewModel = new ViewModel([
                    'error_messages' => $this->ProductFilter->getMessages(),
                ]);
                return $ViewModel;
            }

            $customer_id = $this->CustomerFilter->getValue('customer_id');
            $columns = array(
                'customer_id',
                'email',
                'first_name',
                'last_name',
                'company_name',
                'phone'
            );
            $CustomerData = $this->CustomerTable->fetchCustomer($customer_id, $columns);
            $customerData = (array)$CustomerData;
            $Customer = new Customer();
            $Customer->exchangeArray($customerData);
        } else {
            $Customer = new Customer();
            $Customer->exchangeArray(array());
        }

        // update cart with customer info and validate if exists else create one
        if ($this->Session->offsetExists('cart_id')) {
            $cart_id = $this->Session->offsetGet('cart_id');
            $this->CartFilter->setData(array('cart_id' => $cart_id));
            $this->CartFilter->setValidationGroup('cart_id');
            if ($this->CartFilter->isValid()) {
                $cart_id = $this->CartFilter->getValue('cart_id');
                $data = array(
                    'customer_id' => $Customer->customer_id,
                    'email'       => $Customer->email,
                    'first_name'  => $Customer->first_name,
                    'last_name'   => $Customer->last_name,
                    'phone'       => $Customer->phone
                );
                $this->CartTable->updateCart($cart_id, $data);
            } else {
                $ViewModel = new ViewModel([
                    'error_messages' => $this->CartFilter->getMessages(),
                ]);
                return $ViewModel;
            }
        } else {
            $dateAndTimeNow = date("Y-m-d H:i:s");
            $data = array(
                'customer_id'    => $Customer->customer_id,
                'email'          => $Customer->email,
                'first_name'     => $Customer->first_name,
                'last_name'      => $Customer->last_name,
                'phone'          => $Customer->phone,
                'order_datetime' => $dateAndTimeNow,
            );
            $cart_id = $this->CartTable->insertCart($data);
            $this->Session->offsetSet('cart_id', $cart_id);
        }
        return $cart_id;
    }

    private function updateCartAction($cart_id, $itemPrice, $itemWeight)
    {
        $columns = array(
            'sub_total',
            'total_amount',
            'total_weight'
        );
        $Payments = $this->CartTable->fetchCartWithColumns($cart_id, $columns);
        $cartData = array(
            'sub_total' => $Payments->sub_total + $itemPrice,
            'total_amount' => $Payments->total_amount + $itemPrice,
            'total_weight' => $Payments->sub_total + $itemWeight
        );
        $this->CartTable->updateCart($cart_id, $cartData);

        return $this->redirect()->toRoute('cart', ['action' => 'showCart']);
    }

    public function addOrUpdateItemToCartAction()
    {
        // check if error message from createOrUpdateCart()
        if (is_object($this->createOrUpdateCart())) {
            return $this->createOrUpdateCart();
        } else {
            $cart_id = $this->createOrUpdateCart();
        }
        $params = $this->params()->fromPost();
        $product_id = $params['product_id'];
        $qty = $params['qty'];
        $Product = $this->ProductTable->fetchProduct($product_id);
        $productPrice  = $Product->price;
        $productWeight = $Product->weight;
        $stock_qty = !empty($Product->stock_qty) ? $Product->stock_qty : 1;
        $this->ProductFilter->setBetweenValidator($stock_qty);
        $toFilter = array(
            'product_id' => $product_id,
            'qty' => $qty
        );
        $this->ProductFilter->setData($toFilter);

        if ($this->ProductFilter->isValid()) {
            $product_id = $this->ProductFilter->getValue('product_id');
            $qty = $this->ProductFilter->getValue('qty');
            $itemPrice = $this->ProductService->multiply($productPrice, $qty);
            $itemWeight = $this->ProductService->multiply($productWeight, $qty);
            $CartItem = $this->CartItemTable->fetchCartItemByCartAndProductID($cart_id, $product_id);
            $netItemWeight = $itemWeight;
            $itemSubTotal = $itemPrice;

            if ($CartItem) {
                $netItemWeight = $this->CartService->add($CartItem->weight, $itemWeight);
                $totalQty = $this->CartService->add($CartItem->qty, $qty);
                $itemSubTotal = $this->CartService->add($CartItem->price, $itemPrice);
                $data = array(
                    'weight' => $netItemWeight,
                    'qty' => $totalQty,
                    'price' => $itemSubTotal,
                );
                $this->CartItemTable->updateCartItem($CartItem->cart_item_id, $data);
            } else {
                $data = array(
                    'cart_id' => $cart_id,
                    'product_id' => $product_id,
                    'weight' => $netItemWeight,
                    'qty' => $qty,
                    'unit_price' => $Product->price,
                    'price' => $itemSubTotal,
                );
                $CartItem = new CartItem();
                $CartItem->exchangeArray($data);
                $cartItems = $CartItem->getArrayCopy();
                unset($cartItems['cart_item_id']);
                $this->CartItemTable->insertCartItem($cartItems);
            }
            $this->updateCartAction($cart_id, $itemPrice, $itemWeight);
        } else {
            $ViewModel = new ViewModel([
                'error_messages' => $this->ProductFilter->getMessages(),
            ]);
            return $ViewModel;
        }
    }

    public function updateCartQuantityAction()
    {
        $newQty = $_POST['qtyChangedValue'];
        $cartItemId = $_POST['cartItemId'];
        $productId = $_POST['productId'];
        $stock_qty = $this->ProductTable->fetchProductWithColumns($productId, array('stock_qty'))->stock_qty;
        $stock_qty = !empty($stock_qty) ? $stock_qty : 1;
        $this->ProductFilter->setBetweenValidator($stock_qty);
        $toFilter = array(
            'qty' => $newQty,
            'product_id' => $productId
        );
        $this->ProductFilter->setData($toFilter);
        $this->CartItemFilter->setData(array('cart_item_id' => $cartItemId));
        if ($this->ProductFilter->isValid() && $this->CartItemFilter->isValid()) {
            $newQty = $this->ProductFilter->getValue('qty');
            $productId = $this->ProductFilter->getValue('product_id');
            $productColumns = array(
                'price',
                'weight'
            );
            $Product   = $this->ProductTable->fetchProductWithColumns($productId, $productColumns);
            $newPrice  = $this->ProductService->multiply($newQty, $Product->price);
            $newWeight = $this->ProductService->multiply($newQty, $Product->weight);
            $data = array(
                'qty' => $newQty,
                'price' => $newPrice,
                'weight' => $newWeight
            );
            $this->CartItemTable->updateCartItem($cartItemId,$data);
            $cart_id = $this->Session->offsetGet('cart_id');
            $this->CartFilter->setData(array('cart_id' => $cart_id));
            $this->CartFilter->setValidationGroup('cart_id');
            if ($this->CartFilter->isValid()) {
                $cart_id = $this->CartFilter->getValue('cart_id');
                $shipping_total = $this->CartTable->
                    fetchCartWithColumns($cart_id, array('shipping_total'))->shipping_total;
                $cartItemsColumns = array(
                    "price" => new Expression("SUM(price)"),
                    "weight" => new Expression("SUM(weight)"),
                );
                $CartItem = $this->CartItemTable->fetchCartItemGroupByCartID($cart_id, $cartItemsColumns)->current();
                $newTotalAmount = $this->ProductService->add($shipping_total, $CartItem->price);
                $data = array(
                    'sub_total' => $CartItem->price,
                    'total_amount' => $newTotalAmount,
                    'total_weight' => $CartItem->weight
                );
                $this->CartTable->updateCart($cart_id, $data);
                $result = new JsonModel (array(
                    'newPrice' => $newPrice,
                    'newSubTotal' => $CartItem->price,
                    'newTotalAmount' => $newTotalAmount,
                ));
                return $result;
            } else {
                $ViewModel = new ViewModel([
                    'error_messages' => $this->ProductFilter->getMessages(),
                ]);
                return $ViewModel;
            }
        } else {
            $ViewModel = new ViewModel([
                'error_messages' => $this->ProductFilter->getMessages(),
            ]);
            return $ViewModel;
        }
    }

    public function deleteCartItemAction()
    {
        $cart_item_id = (int)$this->params()->fromRoute('id', null);
        $this->CartItemFilter->setData(array('cart_item_id' => $cart_item_id));
        if ($this->CartItemFilter->isValid()) {
            $cart_item_id = $this->CartItemFilter->getValue('cart_item_id');
            $cartItemsColumns = array(
                "price",
                "weight",
            );
            $CartItem = $this->CartItemTable->fetchCartItemWithColumns($cart_item_id, $cartItemsColumns);
            $cart_id  = $this->Session->offsetGet('cart_id');
            $this->CartFilter->setData(array('cart_id' => $cart_id));
            $this->CartFilter->setValidationGroup('cart_id');
            if ($this->CartFilter->isValid()) {
                $cart_id = $this->CartFilter->getValue('cart_id');
                $cartColumns = array(
                    'sub_total',
                    'total_weight',
                    'total_amount'
                );
                $Cart = $this->CartTable->fetchCartWithColumns($cart_id, $cartColumns);
                $newWeight = $this->CartService->subtract($Cart->total_weight, $CartItem->weight);
                $newSubtotal = $this->CartService->subtract($Cart->sub_total, $CartItem->price);
                $newTotalAmount = $this->CartService->subtract($Cart->total_amount, $CartItem->price);
                $data = array(
                    'sub_total' => $newSubtotal,
                    'total_weight' => $newWeight,
                    'total_amount' => $newTotalAmount
                );
                $this->CartTable->updateCart($cart_id, $data);
                $this->CartItemTable->deleteCartItem($cart_item_id);

                return $this->redirect()->toRoute('cart', ['action' => 'showCart']);
            }
        } else {
            $ViewModel = new ViewModel([
                'error_messages' => $this->CartItemFilter->getMessages(),
            ]);
            return $ViewModel;
        }
    }

    public function showCartAction()
    {
        if ($this->Session->offsetExists('cart_id')) {
            $cart_id = $this->Session->offsetGet('cart_id');
            $this->CartFilter->setData(array('cart_id' => $cart_id));
            $this->CartFilter->setValidationGroup('cart_id');
            if ($this->CartFilter->isValid()) {
                $cart_id = $this->CartFilter->getValue('cart_id');
                $cartColumns = array(
                    'total_amount',
                    'sub_total',
                    'shipping_total'
                );
                $Payments = $this->CartTable->fetchCartWithColumns($cart_id, $cartColumns);
                $itemColumns = array(
                    'cart_item_id',
                    'product_id',
                    'qty',
                    'price',
                );
                $CartItems = $this->CartItemTable->fetchCartItemByCartID($cart_id, $itemColumns)->buffer();
                $productColumns = array(
                    'product_name',
                    'product_desc',
                    'product_thumbnail',
                    'price',
                    'stock_qty'
                );
                $products = array();
                foreach ($CartItems as $key => $CartItem) {
                    $product = $this->ProductTable->fetchProductWithColumns(
                        $CartItem->product_id, $productColumns);
                    $product->product_thumbnail = trim($product->product_thumbnail, "http://localhost/");
                    $products[$key] = $product;
                }
                $ViewModel = new ViewModel([
                    'CartItems' => $CartItems,
                    'Products' => $products,
                    'Payments' => $Payments
                ]);
                return $ViewModel;
            } else {
                $ViewModel = new ViewModel([
                    'error_messages' => $this->CartFilter->getMessages(),
                ]);

                return $ViewModel;
            }
        }
        $ViewModel = new ViewModel([
            'CartItems' => array(),
        ]);

        return $ViewModel;
    }
}