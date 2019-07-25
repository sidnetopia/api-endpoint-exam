<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Job\Controller\Rest;

use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Model\JobItemsTable;
use Job\Model\JobOrderTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class JobController extends AbstractRestfulController
{
//    private $ProductTable;
    private $JobOrderTable;
    private $JobItemsTable;
    private $CartTable;
    private $CartItemTable;
//    private $JobItemsTable;
//    private $CustomerFilter;
//    private $JobFilter;
//    private $Session;

    public function __construct(
//        ProductTable   $productTable,
        JobOrderTable  $JobOrderTable,
        JobItemsTable  $JobItemsTable,
//        CustomerFilter $customerFilter,
//        JobFilter      $jobFilter,
        CartTable      $CartTable,
        CartItemTable      $CartItemTable
    )
    {
//        $this->ProductTable   =  $productTable;
        $this->JobOrderTable  =  $JobOrderTable;
        $this->JobItemsTable  =  $JobItemsTable;
//        $this->JobItemsTable  =  $jobItemsTable;
//        $this->CustomerFilter =  $customerFilter;
//        $this->JobFilter      =  $jobFilter;
//        $this->Session        =  $session;
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
    }

    public function create($data)
    {
        try {
            $Cart = $this->CartTable->fetchCart(['*'])->current();
            $cartArray = get_object_vars($Cart);
            $cartId = $cartArray['cart_id'];
            unset($cartArray['cart_id']);

            $jobOrderId = $this->JobOrderTable->insertJobOrder($cartArray);

            $CartItems = $this->CartItemTable->fetchCartItems(['*'], ['cart_id' => $cartId] );
            foreach ($CartItems as $CartItem) {
                unset($CartItem->cart_item_id);
                unset($CartItem->cart_id);
                $CartItem->job_order_id = $jobOrderId;
                $this->JobItemsTable->insertJobItem(get_object_vars($CartItem));
            }
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }

        try{
            $this->CartItemTable->deleteCartItems($cartId);
            $this->CartTable->deleteCart($cartId);

            return new ApiProblemResponse(new ApiProblem(202, 'Accepted: '));

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }
    }
}