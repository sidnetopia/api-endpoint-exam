<?php
namespace Shipping\Controller\Rest;

use Cart\Model\CartTable;
use Shipping\Service\ShippingService;
use Shipping\Filter\ShippingFilter;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ShippingController extends AbstractRestfulController
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

        $this->CartTable       = $CartTable;
        $this->ShippingService = $ShippingService;
        $this->ShippingFilter  = $ShippingFilter;
    }

    /**
     * Get shipping prices
     *
     * @return mixed|JsonModel
     */
    public function getList()
    {
        $total_weight = $this->CartTable->fetchCart(['total_weight'])->current()->total_weight;

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
     *
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function create($data)
    {
        try {
            $Cart = $this->CartTable->fetchCart(['cart_id', 'total_weight'])->current();
            $this->ShippingFilter->setData($data);
            if (!$this->ShippingFilter->isValid()) {
                $error_messages = $this->ShippingFilter->getErrorMessage();
                return new ApiProblemResponse(new ApiProblem(400, $error_messages));
            }

            $data = $this->ShippingFilter->getValues();

            $this->ShippingService->setShippingMethod($data['shipping_mehod']);
            $shippingPrice = $this->ShippingService->calculateShippingFee($Cart->total_weight);

            $data['shipping_total'] = $shippingPrice;
            $data['total_amount'] = new Expression("total_amount + {$shippingPrice}");

            $this->CartTable->updateCart($data, ['cart_id' => $Cart->cart_id]);

            return new ApiProblemResponse(new ApiProblem(202, 'Accepted'));
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }
    }

    public function options()
    {
    }
}