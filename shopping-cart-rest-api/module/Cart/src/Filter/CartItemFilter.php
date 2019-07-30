<?php
namespace Cart\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\NotEmpty;

class CartItemFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'product_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
        $this->add(array(
            'name' => 'qty',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
    }

    public function getErrorMessage()
    {
        $error_messages = '';
        foreach($this->getMessages() as $key=>$value) {
            $error_messages = $error_messages.$key;
            foreach ($value as $messages) {
                $error_messages = $error_messages." - ".$messages.",";
            }
        }

        return $error_messages;
    }
}
