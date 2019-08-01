<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $EventManager        = $e->getApplication()->getEventManager();
        $ModuleRouteListener = new ModuleRouteListener();
        $ModuleRouteListener->attach($EventManager);

//        //Attach render errors
//        $EventManager->attach(MvcEvent::EVENT_RENDER_ERROR, function($e)  {
//            if ($e->getParam('exception')) {
//                $this->exception( $e->getParam('exception') ) ; //Custom error render function.
//            }
//        } );
//        //Attach dispatch errors
//        $EventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e)  {
//            if ($e->getParam('exception')) {
//                $this->exception( $e->getParam('exception') ) ;//Custom error render function.
//            }
//        } );
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
//
//    public function exception($e) {
//        echo "<span style='font-family: courier new; padding: 2px 5px; background:red; color: white;'> " . $e->getMessage() . '</span><br/>' ;
//        echo "<pre>" . $e->getTraceAsString() . '</pre>' ;
//    }
}
