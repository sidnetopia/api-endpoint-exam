<?php
namespace Shipping\Controller\Rest;

use Application\Controller\CoreController;
use Cart\Model\CartTable;
use Shipping\Service\ShippingService;
use Shipping\Filter\ShippingFilter;
use Zend\Db\Sql\Expression;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ShippingController extends CoreController
{
    private $ShippingService;
    private $ShippingFilter;
    private $CartTable;

    public function __construct
    (
        CartTable $CartTable,
        ShippingService $ShippingService,
        ShippingFilter $ShippingFilter
    )
    {

        $this->CartTable = $CartTable;
        $this->ShippingService = $ShippingService;
        $this->ShippingFilter = $ShippingFilter;
    }

    /**
     * Get shipping prices
     * Return error if failed
     *
     * @return mixed|JsonModel
     */
    public function getList()
    {
        $total_weight = $this->CartTable->fetchCart(['total_weight'])->current()->total_weight;

        $total_weight = $this->ShippingService->prepareWeight($total_weight);

        $this->ShippingService->setShippingMethod('Ground');
        $groundShippingPayment = $this->ShippingService->calculateShippingFee($total_weight);

        $this->ShippingService->setShippingMethod('Expedited');
        $expeditedShippingPayment = $this->ShippingService->calculateShippingFee($total_weight);

        return new JsonModel(['shippingPayment' => [
            'ground' => $groundShippingPayment,
            'expedited' => $expeditedShippingPayment
        ]]);
    }

    /**
     * Update shipping details in cart
     * Return error if failed
     *
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function create($data)
    {
        try {
            $Cart = $this->CartTable->fetchCart(['cart_id', 'total_weight'])->current();
            $this->ShippingFilter->setData($data);
            if ($this->ShippingFilter->getErrors())
                return $this->ShippingFilter->getErrors();

            $data = $this->ShippingFilter->getValues();

            $this->ShippingService->setShippingMethod($data['shipping_mehod']);
            $total_weight = $this->ShippingService->prepareWeight($Cart->total_weight);
            $shippingPrice = $this->ShippingService->calculateShippingFee($total_weight);

            $data['shipping_total'] = $shippingPrice;
            $data['total_amount'] = new Expression("total_amount + {$shippingPrice}");

            $this->CartTable->updateCart($data, ['cart_id' => $Cart->cart_id]);

            $response = ['code' => 200, 'details' => 'Accepted'];
        } catch (\Exception $e) {
            $response = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));
    }
}