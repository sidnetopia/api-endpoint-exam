<?php
namespace Job\Model;

class JobOrder
{
    public $job_order_id;
    public $customer_id;
    public $order_datetime;
    public $sub_total;
    public $taxable_amount;
    public $discount;
    public $tax;
    public $shipping_total;
    public $total_amount;
    public $total_weight;
    public $company_name;
    public $email;
    public $first_name;
    public $last_name;
    public $phone;
    public $shipping_method;
    public $shipping_name;
    public $shipping_address1;
    public $shipping_address2;
    public $shipping_address3;
    public $shipping_city;
    public $shipping_state;
    public $shipping_country;

    public function exchangeArray(array $data)
    {
        $this->job_order_id = !empty($data['job_order_id']) ? $data['job_order_id'] : null;
        $this->customer_id = !empty($data['customer_id']) ? $data['customer_id'] : null;
        $this->order_datetime = !empty($data['order_datetime']) ? $data['order_datetime'] : null;
        $this->sub_total = !empty($data['sub_total']) ? $data['sub_total'] : null;
        $this->taxable_amount = !empty($data['taxable_amount']) ? $data['taxable_amount'] : null;
        $this->discount = !empty($data['discount']) ? $data['discount'] : null;
        $this->tax = !empty($data['tax']) ? $data['tax'] : null;
        $this->shipping_total = !empty($data['shipping_total']) ? $data['shipping_total'] : null;
        $this->total_amount = !empty($data['total_amount']) ? $data['total_amount'] : null;
        $this->total_weight = !empty($data['total_weight']) ? $data['total_weight'] : null;
        $this->company_name = !empty($data['company_name']) ? $data['company_name'] : null;
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->first_name = !empty($data['first_name']) ? $data['first_name'] : null;
        $this->last_name = !empty($data['last_name']) ? $data['last_name'] : null;
        $this->phone = !empty($data['phone']) ? $data['phone'] : null;
        $this->shipping_method = !empty($data['shipping_method']) ? $data['shipping_method'] : null;
        $this->shipping_name = !empty($data['shipping_name']) ? $data['shipping_name'] : null;
        $this->shipping_address1 = !empty($data['shipping_address1']) ? $data['shipping_address1'] : null;
        $this->shipping_address2 = !empty($data['shipping_address2']) ? $data['shipping_address2'] : null;
        $this->shipping_address3 = !empty($data['shipping_address3']) ? $data['shipping_address3'] : null;
        $this->shipping_city = !empty($data['shipping_city']) ? $data['shipping_city'] : null;
        $this->shipping_state = !empty($data['shipping_state']) ? $data['shipping_state'] : null;
        $this->shipping_country = !empty($data['shipping_country']) ? $data['shipping_country'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}