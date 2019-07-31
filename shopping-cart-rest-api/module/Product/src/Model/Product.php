<?php
namespace Product\Model;

class Product
{
    public function getImagePath($productDetailsArray, $hostname)
    {
        if (isset($productDetailsArray['product_thumbnail']))
            $productDetailsArray['product_thumbnail'] = trim($productDetailsArray['product_thumbnail'], $hostname);
        if (isset($productDetailsArray['product_image']))
            $productDetailsArray['product_image'] = trim($productDetailsArray['product_image'], $hostname);

        return $productDetailsArray;
    }
}