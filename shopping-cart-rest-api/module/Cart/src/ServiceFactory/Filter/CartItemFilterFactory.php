<?php
namespace Cart\ServiceFactory\Filter;

use Cart\Filter\CartItemFilter;
use Psr\Container\ContainerInterface;

class CartItemFilterFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $DbAdapter = $Container->get('test');
        return new CartItemFilter($DbAdapter);
    }
}