<?php
namespace Product\Filter;

use Application\Filter\CoreFilter;

class ProductFilter extends CoreFilter
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
