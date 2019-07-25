<?php
namespace Shipping\ServiceFactory\Storage;

use Psr\Container\ContainerInterface;
use Zend\Session\Container;

class ShippingSessionContainerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        return new Container();
    }
}