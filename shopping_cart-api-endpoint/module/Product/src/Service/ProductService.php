<?php
namespace Product\Service;

class ProductService
{
    public function multiply($unit, $qty)
    {
        $total = $unit * $qty;
        return $total;
    }

    public function subtract($total, $ordered)
    {
        $total = $total - $ordered;
        return $total;
    }

    public function add($stock_qty, $deletedQty)
    {
        $total = $stock_qty + $deletedQty;
        return $total;
    }
}