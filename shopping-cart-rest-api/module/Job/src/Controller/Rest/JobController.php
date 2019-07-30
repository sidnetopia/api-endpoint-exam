<?php
namespace Job\Controller\Rest;

use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Job\Model\JobItemsTable;
use Job\Model\JobOrderTable;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class JobController extends AbstractRestfulController
{
    private $JobOrderTable;
    private $JobItemsTable;
    private $CartTable;
    private $CartItemTable;
    private $hostname;

    public function __construct(
        JobOrderTable  $JobOrderTable,
        JobItemsTable  $JobItemsTable,
        CartTable      $CartTable,
        CartItemTable  $CartItemTable,
        $hostname
    )
    {
        $this->JobOrderTable  =  $JobOrderTable;
        $this->JobItemsTable  =  $JobItemsTable;
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->hostname = $hostname;
    }

    /**
     * Get job items and job order details
     *
     * @return mixed|JsonModel|ApiProblemResponse
     */
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
                ['job_order_id' => $JobOrder->job_order_id], true,[
                    'product_thumbnail',
                    'price',
                    'product_desc'
                ]);

            $jobItemArray = [];
            foreach ($JobItems as $key => $value) {
                $jobItemArray[$key] = $value;
                $jobItemArray[$key]['product_thumbnail'] = trim($value['product_thumbnail'], $this->hostname);
            }

            return new JsonModel(['jobItems' => $jobItemArray, 'jobOrderDetails' => get_object_vars($JobOrder)]);
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    /**
     * Transfer cart details and items to job order and job items.
     * Delete cart and create new one.
     *
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function create($data)
    {
        try {
            $Cart = $this->CartTable->fetchCart()->current();

            if ($Cart->shipping_total <= 0) {
                return new ApiProblemResponse(new ApiProblem(403, 'Forbidden'));
            }

            $cartArray = get_object_vars($Cart);
            $cartId = $cartArray['cart_id'];
            unset($cartArray['cart_id']);

            $jobOrderId = $this->JobOrderTable->insertJobOrder($cartArray);

            $CartItems = $this->CartItemTable->fetchCartItems([
                'product_id',
                'weight',
                'qty',
                'unit_price',
                'price'
                ], ['cart_id' => $cartId]);

            foreach ($CartItems as $CartItem) {
                $CartItem->job_order_id = $jobOrderId;
                $this->JobItemsTable->insertJobItem(get_object_vars($CartItem));
            }

            return $this->deleteAndCreateCart($cartId);
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    /**
     * Delete cart and create new one.
     *
     * @param $cartId
     * @return ApiProblemResponse
     */
    private function deleteAndCreateCart($cartId)
    {
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

    public function options()
    {
    }
}