<?php
namespace Cart\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\NotEmpty;

class CartFilter extends InputFilter
{
    private $DbAdapter;

    public function __construct($dbAdapter)
    {
        $this->DbAdapter = $dbAdapter;
        $this->add(array(
            'name' => 'cart_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Cart Id is required.',
                        ),
                    ),
                ),
                array(
                    'name' => 'Db\RecordExists',
                    'options' => array(
                        'table'    => 'carts',
                        'field'    => 'cart_id',
                        'adapter'  => $this->DbAdapter,
                        'messages' => array(
                            RecordExists::ERROR_NO_RECORD_FOUND => 'Cart Id is not found.',
                        ),
                    ),
                ),
            ),
        ));
    }
}
