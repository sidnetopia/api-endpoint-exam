<?php
namespace Job\Filter;

use Zend\InputFilter\InputFilter;

class JobFilter extends InputFilter
{
    private $DbAdapter;

    public function __construct($dbAdapter)
    {
        $this->DbAdapter = $dbAdapter;
        $this->add(array(
            'name' => 'job_order_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
    }
}