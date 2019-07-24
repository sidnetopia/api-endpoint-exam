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
            $Cart = $this->CartTable->fetchCart()->current();
            $CartItems = $this->CartItemTable->fetchCartItems($Cart->cart_id);

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

    public function delete($data)
    {
    }
}