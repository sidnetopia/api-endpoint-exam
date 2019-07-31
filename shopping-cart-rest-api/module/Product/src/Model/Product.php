<?php
namespace Product\Model;

class Product
{
//    public $product_id;
//    public $product_name;
//    public $product_desc;
//    public $product_image;
//    public $product_thumbnail;
//    public $weight;
//    public $price;
//    public $stock_qty;
//    public $taxable_flag;
//
//    public function exchangeArray(array $data)
//    {
//        $this->product_id = !empty($data['product_id']) ? $data['product_id'] : null;
//        $this->product_name = !empty($data['product_name']) ? $data['product_name'] : '';
//        $this->product_desc = !empty($data['product_desc']) ? $data['product_desc'] : '';
//        $this->product_image = !empty($data['product_image']) ? $data['product_image'] : '';
//        $this->product_thumbnail = !empty($data['product_thumbnail']) ? $data['product_thumbnail'] : '';
//        $this->weight = !empty($data['weight']) ? $data['weight'] : 0;
//        $this->price = !empty($data['price']) ? $data['price'] : 0;
//        $this->stock_qty = !empty($data['stock_qty']) ? $data['stock_qty'] : 0;
//        $this->taxable_flag = !empty($data['taxable_flag']) ? $data['taxable_flag'] : 'n';
//    }
//
//    public function getArrayCopy()
//    {
//        return get_object_vars($this);
//    }
    public function getImagePath($productDetailsArray, $hostname)
    {
        if (isset($productDetailsArray['product_thumbnail']))
            $productDetailsArray['product_thumbnail'] = trim($productDetailsArray['product_thumbnail'], $hostname);
        if (isset($productDetailsArray['product_image']))
            $productDetailsArray['product_image'] = trim($productDetailsArray['product_image'], $hostname);

        return $productDetailsArray;
    }
}