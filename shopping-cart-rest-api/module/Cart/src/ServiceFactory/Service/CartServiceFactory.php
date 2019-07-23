<?php
namespace Cart\ServiceFactory\Service;

use Cart\Service\CartService;

class CartServiceFactory
{
    public function __invoke()
    {
        return new CartService();
    }
}