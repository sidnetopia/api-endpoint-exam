<?php
namespace Job\ServiceFactory\Storage;

use Psr\Container\ContainerInterface;
use Zend\Session\Container;

class JobSessionContainerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        return new Container();
    }
}