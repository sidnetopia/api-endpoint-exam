<?php
namespace Shipping\Service;

use Shipping\Model\ShippingTable;

class ShippingService
{
    private $ShippingTable;
    private $ShippingList;

    public function __construct(ShippingTable $ShippingTable)
    {
        $this->ShippingTable = $ShippingTable->fetchShipping()->buffer();
    }

    public function calculateShippingFee($weight, $price=0)
    {
        $shippingSize = count($this->ShippingList)-1;
        $maxWeight = $this->ShippingList[$shippingSize]['max_weight'];

        if ($weight > $maxWeight) {
            $shippingRate = $this->ShippingList[$shippingSize]['shipping_rate'];
            $price = $price + $shippingRate;
            $extraWeight = $weight - $maxWeight;

            return $this->calculateShippingFee($extraWeight, $price );
        }

        if($weight<0){
            return $price;
        }

        foreach ($this->ShippingList as $shipping) {
            if($weight >= $shipping['min_weight'] && $weight <= $shipping['max_weight']) {
                $price = $price + $shipping['shipping_rate'];
                $extraWeight = $weight-$shipping['max_weight'];

                return $this->calculateShippingFee($extraWeight, $price);
            }
        }
    }

    public function setShipping($shippingMethod)
    {
        $this->ShippingTable->rewind();
        $this->ShippingList = array();
        foreach($this->ShippingTable as $ShippingDetails) {
            if($ShippingDetails->shipping_method === $shippingMethod) {
                $this->ShippingList[] = $ShippingDetails;
            }
        }

    }
}