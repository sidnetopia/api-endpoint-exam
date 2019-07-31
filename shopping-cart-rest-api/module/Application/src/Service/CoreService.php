<?php
namespace Application\Service;

class CoreService
{
    public function transformToArrayWithFunction($Object, $function, $additionalParams = null)
    {
        $array = [];
        foreach ($Object as $key => $value) {
            $array[$key] = $value;
            $array[$key] = $function($array[$key], $additionalParams);
        }

        return $array;
    }
}