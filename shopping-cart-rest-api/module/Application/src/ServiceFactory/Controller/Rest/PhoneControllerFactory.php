<?php
namespace Application\ServiceFactory\Controller\Rest;

use Application\Controller\Rest\PhoneController;
use Application\Filter\PhoneFilter;
use Application\Model\Phone;
use Application\Model\PhoneTable;
use Application\Service\PhoneService;
use Psr\Container\ContainerInterface;

class PhoneControllerFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $Container = $Container->getServiceLocator();
        $PhoneTable = $Container->get(PhoneTable::class);
        $Phone = $Container->get(Phone::class);
        $PhoneService = $Container->get(PhoneService::class);
        $PhoneFilter = $Container->get(PhoneFilter::class);

        return new PhoneController($PhoneTable, $Phone, $PhoneService, $PhoneFilter);
    }
}