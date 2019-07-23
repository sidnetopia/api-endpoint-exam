<?php
namespace Cart\ServiceFactory\Filter;

use Cart\Filter\CartFilter;
use Psr\Container\ContainerInterface;

class CartFilterFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $DbAdapter = $Container->get('test');
        return new CartFilter($DbAdapter);
    }
}