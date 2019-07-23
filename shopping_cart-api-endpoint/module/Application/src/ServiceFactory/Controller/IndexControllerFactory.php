<?php
namespace Application\ServiceFactory\Controller;

use Application\Controller\IndexController;
use Application\Model\PhoneTable;
use Application\Service\PhoneService;
use Psr\Container\ContainerInterface;

class IndexControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        return new IndexController();
    }
}