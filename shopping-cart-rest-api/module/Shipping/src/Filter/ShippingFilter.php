<?php
namespace Shipping\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Regex;

class ShippingFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'shipping_name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                  'name' => 'StringLength',
                  'options' => array(
                        'min' => 1,
                        'max' => 35,
                  ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z .]+$/',
                        'messages' => array(
                            Regex::NOT_MATCH => 'Text only'
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_address1',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_address2',
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_address3',
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_city',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z]+$/',
                        'messages' => array(
                            Regex::NOT_MATCH => 'Text only'
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_state',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(

                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z]+$/',
                        'messages' => array(
                            Regex::NOT_MATCH => 'Text only'
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_country',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 35,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z]+$/',
                        'messages' => array(
                            Regex::NOT_MATCH => 'Text only'
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'shipping_mehod',
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
