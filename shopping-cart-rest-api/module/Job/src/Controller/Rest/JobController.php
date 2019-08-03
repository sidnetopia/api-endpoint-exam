<?php
namespace Job\Controller\Rest;

use Application\Controller\CoreController;
use Application\Service\CoreService;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Cart\Service\CartService;
use Job\Model\JobItemsTable;
use Job\Model\JobOrderTable;
use Product\Model\Product;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class JobController extends CoreController
{
    private $JobOrderTable;
    private $JobItemsTable;
    private $CartTable;
    private $CartItemTable;
    private $hostname;
    private $CoreService;
    private $Product;
    private $CartService;

    public function __construct(
        JobOrderTable $JobOrderTable,
        JobItemsTable $JobItemsTable,
        CartTable $CartTable,
        CartItemTable $CartItemTable,
        $hostname,
        CoreService $CoreService,
        Product $Product,
        CartService $CartService
    )
    {
        $this->JobOrderTable = $JobOrderTable;
        $this->JobItemsTable = $JobItemsTable;
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->hostname = $hostname;
        $this->CoreService = $CoreService;
        $this->Product = $Product;
        $this->CartService = $CartService;
    }

    /**
     * Get job items and job order details
     * Return error if failed
     *
     * @return mixed|JsonModel|ApiProblemResponse
     */
    public function getList()
    {
        try {
            $JobOrder = $this->JobOrderTable->fetchJobOrder([ // single and multiple columns
                'job_order_id',
                'shipping_name',
                'shipping_address1',
                'shipping_address2',
                'shipping_address3',
                'sub_total',
                'shipping_total',
                'total_amount'
            ])->current(); // ->to models
            $JobItems = $this->JobItemsTable->fetchJobItems(['qty', 'item_price' => 'price'],
                ['job_order_id' => $JobOrder->job_order_id], [
                    'product_thumbnail',
                    'price',
                    'product_desc'
                ]);

            $jobItemArray = $this->CoreService
                ->transformToArrayWithFunction($JobItems, array($this->Product, 'getImagePath'), $this->hostname);

            return new JsonModel(['jobItems' => $jobItemArray, 'jobOrderDetails' => get_object_vars($JobOrder)]);
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }
    }

    /**
     * Transfer cart details and items to job order and job items.
     * Delete cart and create new one.
     * Return error if failed
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

            $jobOrderId = 156;

            $CartItems = $this->CartItemTable->fetchCartItems([
                'product_id',
                'weight',
                'qty',
                'unit_price',
                'price'
            ], ['cart_id' => $cartId]);

            foreach ($CartItems as $CartItem) { //gawan ng function
                $CartItem->job_order_id = $jobOrderId;
                $this->JobItemsTable->insertJobItem(get_object_vars($CartItem));
            }

//            $this->JobItemsTable->insertMultipleCartItemsToJobItems($CartItems, $jobOrderId);

            $response = $this->CartService->deleteAndCreateCart($this->CartItemTable, $this->CartTable, $cartId);
        } catch (\Exception $e) {
            $response = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));
    }
}