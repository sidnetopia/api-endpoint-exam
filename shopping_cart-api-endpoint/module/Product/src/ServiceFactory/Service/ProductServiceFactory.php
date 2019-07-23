<?php
namespace Product\ServiceFactory\Service;

use Product\Service\ProductService;

class ProductServiceFactory
{
    public function __invoke()
    {
        return new ProductService();
    }
}