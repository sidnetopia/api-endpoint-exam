<?php

namespace Shipping\Service;

use Shipping\Model\ShippingTable;

class ShippingService
{
    private $ShippingTable;
    private $ShippingList;
    private $ShippingListSize;

    public function __construct(ShippingTable $ShippingTable)
    {
        $this->ShippingTable = $ShippingTable->fetchShipping()->buffer();
    }

    public function calculateShippingFee($weight, $price = 0)
    {

        $maxWeight = $this->ShippingList[$this->ShippingListSize]['max_weight'];

        if (fmod($weight , 1) > 0) {
            $shippingRate = $this->ShippingList[0]['shipping_rate'];
            $price = $price + $shippingRate;
            $extraWeight = $weight - (fmod($weight , 1));

            return $this->calculateShippingFee($extraWeight, $price);
        }

        if ($weight > $maxWeight) {
            $shippingRate = $this->ShippingList[$this->ShippingListSize]['shipping_rate'];
            $price = $price + $shippingRate;
            $extraWeight = $weight - $maxWeight;

            return $this->calculateShippingFee($extraWeight, $price);
        }

        if ($weight <= 0) {
            return $price;
        }


        foreach ($this->ShippingList as $shipping) {
            if ($weight >= $shipping['min_weight'] && $weight <= $shipping['max_weight']) {
                $price = $price + $shipping['shipping_rate'];
                $extraWeight = $weight - $shipping['max_weight'];

                return $this->calculateShippingFee($extraWeight, $price);
            }
        }
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->ShippingTable->rewind();
        $this->ShippingList = [];
        foreach ($this->ShippingTable as $ShippingDetails) {
            if ($ShippingDetails->shipping_method === $shippingMethod) {
                $this->ShippingList[] = get_object_vars($ShippingDetails);
            }
        }

        $this->ShippingListSize = count($this->ShippingList) - 1;
    }
}