<?php
namespace Product\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class ProductFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'productId',
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
    }

    public function sanitize($data)
    {
        $this->setData($data);
        return $this->getValues();
    }
}
