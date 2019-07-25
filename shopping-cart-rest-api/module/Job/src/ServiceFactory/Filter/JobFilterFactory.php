<?php
namespace Job\ServiceFactory\Filter;

use Job\Filter\JobFilter;
use Psr\Container\ContainerInterface;

class JobFilterFactory
{
    public function __invoke(ContainerInterface $Container)
    {
        $DbAdapter = $Container->get('test');
        return new JobFilter($DbAdapter);
    }
}