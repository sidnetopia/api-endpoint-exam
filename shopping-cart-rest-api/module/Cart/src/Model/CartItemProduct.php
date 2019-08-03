<?php
namespace Cart\Model;

use Product\Model\Product;

class CartItemProduct extends Product
{
    public $cart_item_id;
    public $cart_id;
    public $product_id;
    public $weight;
    public $qty;
    public $unit_price;
    public $price;

    public function exchangeArray(array $data)
    {
        $this->cart_item_id       = !empty($data['cart_item_id']) ? $data['cart_item_id'] : null;
        $this->cart_id            = !empty($data['cart_id']) ? $data['cart_id'] : null;
        $this->product_id         = !empty($data['product_id']) ? $data['product_id'] : null;
        $this->weight             = !empty($data['weight']) ? $data['weight'] : null;
        $this->qty                = !empty($data['qty']) ? $data['qty'] : null;
        $this->unit_price         = !empty($data['unit_price']) ? $data['unit_price'] : null;
        $this->price              = !empty($data['price']) ? $data['price'] : null;
        $this->product_id         = isset($data['product_id']) ? $data['product_id'] : null;
        $this->product_name       = isset($data['product_name']) ? $data['product_name'] : null;
        $this->product_desc       = isset($data['product_desc']) ? $data['product_desc'] : null;
        $this->product_image      = isset($data['product_image']) ? $data['product_image'] : null;
        $this->product_thumbnail  = isset($data['product_thumbnail']) ? $data['product_thumbnail'] : null;
        $this->weight             = isset($data['weight']) ? $data['weight'] : null;
        $this->price              = isset($data['price']) ? $data['price'] : null;
        $this->stock_qty          = isset($data['stock_qty']) ? $data['stock_qty'] : null;
        $this->taxable_flag       = isset($data['taxable_flag']) ? $data['taxable_flag'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}