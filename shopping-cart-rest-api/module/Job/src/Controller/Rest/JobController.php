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
use Product\Model\Product;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class JobController extends AbstractRestfulController
{
    private $Product;
    private $JobOrderTable;
    private $JobItemsTable;
    private $CartTable;
    private $CartItemTable;
    private $hostname;
//    private $JobItemsTable;
//    private $CustomerFilter;
//    private $JobFilter;
//    private $Session;

    public function __construct(
        Product   $Product,
        JobOrderTable  $JobOrderTable,
        JobItemsTable  $JobItemsTable,
//        CustomerFilter $customerFilter,
//        JobFilter      $jobFilter,
        CartTable      $CartTable,
        CartItemTable      $CartItemTable,
        $hostname
    )
    {
        $this->Product   =  $Product;
        $this->JobOrderTable  =  $JobOrderTable;
        $this->JobItemsTable  =  $JobItemsTable;
//        $this->JobItemsTable  =  $jobItemsTable;
//        $this->CustomerFilter =  $customerFilter;
//        $this->JobFilter      =  $jobFilter;
//        $this->Session        =  $session;
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->hostname = $hostname;
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
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }

        try{
            $this->CartItemTable->deleteCartItems($cartId);
            $this->CartTable->deleteCart($cartId);

            $data = array(
                'order_datetime' => new Expression("NOW()"),
            );

            $this->CartTable->insertCart($data);

            return new ApiProblemResponse(new ApiProblem(202, 'Accepted: '));

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    public function getList()
    {
        try {
            $JobOrder = $this->JobOrderTable->fetchJobOrder(
                [
                    'job_order_id',
                    'shipping_name',
                    'shipping_address1',
                    'shipping_address2',
                    'shipping_address3',
                    'sub_total',
                    'shipping_total',
                    'total_amount'
                    ])->current();
            $JobItems = $this->JobItemsTable->fetchJobItems(['qty', 'item_price' => 'price'],
                ['job_order_id' => $JobOrder->job_order_id], true,['product_thumbnail', 'price', 'product_desc']);


            $jobItemArray = array();
            foreach ($JobItems as $key => $value) {
                $jobItemArray[$key] = $value;
                $jobItemArray[$key]['product_thumbnail'] = $this->Product
                    ->getImagePath($value['product_thumbnail'], $this->hostname);
            }

            return new JsonModel(['jobItems' => $jobItemArray, 'jobOrderDetails' => get_object_vars($JobOrder)]);
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    public function options()
    {
        return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error' ));
    }
}