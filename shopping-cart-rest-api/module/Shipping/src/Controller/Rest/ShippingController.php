<?php
/**
ORDER PRECEDENCE FOR USING NAMESPACE
Controller->Model->Table->Service->Filter->Session
MODULES
Cart->CartItems->Products->Customers->JobOrder->JobItems->Shipping->Payment
 **/
namespace Shipping\Controller\Rest;

use Cart\Model\CartTable;
use Shipping\Model\ShippingTable;
use Cart\Filter\CartFilter;
use Cart\Service\CartService;
use Shipping\Service\ShippingService;
use Customer\Filter\CustomerFilter;
use Shipping\Filter\ShippingFilter;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ShippingController extends AbstractRestfulController
{
//    private $Session;
//    private $ShippingTable;
//    private $CartService;
    private $ShippingService;
//    private $CartFilter;
//    private $CustomerFilter;
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


    public function create($data)
    {
        try {
            $Cart = $this->CartTable->fetchCart(['cart_id', 'total_weight'])->current();
            $this->ShippingFilter->setData($data);
            if (!$this->ShippingFilter->isValid()) {
                $error_messages = '';
                foreach($this->ShippingFilter->getMessages() as $key=>$value) {
                    $error_messages = $error_messages.$key;
                    foreach ($value as $messages) {
                        $error_messages = $error_messages." - ".$messages.",";
                    }
                }

                return new ApiProblemResponse(new ApiProblem(400, $error_messages));
            }

            $data = $this->ShippingFilter->getValues();
            $this->ShippingService->setShipping($data['shipping_mehod']);
            $shippingPrice = $this->ShippingService->calculateShippingFee($Cart->total_weight);
            $data['shipping_total'] = $shippingPrice;
            $data['total_amount'] = new Expression("total_amount + {$shippingPrice}");
            $this->CartTable->updateCart($data, ['cart_id' => $Cart->cart_id]);

            return new ApiProblemResponse(new ApiProblem(202, 'Accepted'));

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }
    }

    public function getList()
    {
        $total_weight = $this->CartTable->fetchCart(['total_weight'])->current()->total_weight;
        $this->ShippingService->setShipping('Ground');
        $groundShippingPayment = $this->ShippingService->calculateShippingFee($total_weight);
        $this->ShippingService->setShipping('Expedited');
        $expeditedShippingPayment = $this->ShippingService->calculateShippingFee($total_weight);

        return new JsonModel(['shippingPayment' => ['ground' => $groundShippingPayment,
            'expedited' => $expeditedShippingPayment
        ]]);
    }

    public function options()
    {
        return new ApiProblemResponse(new ApiProblem(201, 'Internal Server Error' ));
    }
}