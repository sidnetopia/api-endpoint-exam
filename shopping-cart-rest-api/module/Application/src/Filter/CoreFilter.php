<?php
namespace Application\Filter;

use Zend\InputFilter\InputFilter;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class CoreFilter extends InputFilter
{
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

    public function getErrors()
    {
        if (!$this->isValid()) {
            $error_messages = $this->getErrorMessage();
            //array
            return new ApiProblemResponse(new ApiProblem(400, $error_messages)); //sa controller apiproblemresponse
        }

        return;
    }
}
