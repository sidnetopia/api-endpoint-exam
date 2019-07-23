<?php
namespace Cart\Service;

class CartService
{
    public function add($existing, $new)
    {
        $total = $existing + $new;
        return $total;
    }

    public function subtract($existing, $new)
    {
        $total = $existing - $new;
        return $total;
    }
}