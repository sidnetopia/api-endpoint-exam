<?php
namespace Cart;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $EventManager        = $e->getApplication()->getEventManager();
        $ModuleRouteListener = new ModuleRouteListener();
        $ModuleRouteListener->attach($EventManager);

        # SESSION FOR BASE LAYOUT
        $ViewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $Session = new Container();
        $ViewModel->customer_id = $Session->offsetExists('customer_id') ? $Session->offsetGet('customer_id') : null;
        $ViewModel->customer_name = $Session->offsetExists('customer_name') ? $Session->offsetGet('customer_name') : null;
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
