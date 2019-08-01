<?php
namespace Product\Filter;

use Application\Filter\CoreFilter;

class ProductFilter extends CoreFilter
{
    public function __construct()
    {
        $this->add(array( //int
            'name' => 'product_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
    }

    public function addQtyValidator($stockQty)
    {
        $this->add(array(
            'name' => 'qty',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Between',
                    'options' => array(
                        'min' => 1,
                        'max' => $stockQty
                    ),
                ),
            ),
        ));
    }

    public function sanitize($data)
    {
        $this->setData($data);

        return $this->getValues();
    }
}
