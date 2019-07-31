<?php
namespace Application\Service;

class CoreService
{
    public function transformToArrayWithFunction($Object, $function, $additionalParams = null)
    {
        $array = [];
        if (is_array($additionalParams))
            foreach ($Object as $key => $value) {
                $array[$key] = $value;
                $array[$key] = $function($array[$key], ...$additionalParams);
            }
        else
            foreach ($Object as $key => $value) {
                $array[$key] = $value;
                $array[$key] = $function($array[$key], $additionalParams);
            }

        return $array;
    }
}