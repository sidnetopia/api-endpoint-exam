<?php
namespace Cart\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\NotEmpty;

class CartItemFilter extends InputFilter
{
    private $DbAdapter;

    public function __construct($dbAdapter)
    {
        $this->DbAdapter = $dbAdapter;
        $this->add(array(
            'name' => 'cart_item_id',
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
                            NotEmpty::IS_EMPTY => 'Cart item ID is required.',
                        ),
                    ),
                ),
                array(
                    'name' => 'Db\RecordExists',
                    'options' => array(
                        'table'    => 'cart_items',
                        'field'    => 'cart_item_id',
                        'adapter'  => $this->DbAdapter,
                        'messages' => array(
                            RecordExists::ERROR_NO_RECORD_FOUND => 'Cart item ID is required.',
                        ),
                    ),
                ),
            ),
        ));
    }
}
