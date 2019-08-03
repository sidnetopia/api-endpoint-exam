<?php
namespace ProductTest\Controller;

use Product\Controller\Rest\ProductController;
use Prophecy\Argument;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Product\Model\ProductTable;
use Zend\ServiceManager\ServiceManager;

class ProductControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $ProductTable;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
        // Grabbing the full application configuration:
            include __DIR__ . '/../../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();

//        $this->configureServiceManager($this->getApplicationServiceLocator());
//        $services = $this->getApplicationServiceLocator();
//        $config = $services->get('config');
//        unset($config['db']);
//        $services->setAllowOverride(true);
//        $services->setService('config', $config);
//        $services->setAllowOverride(false);
    }

//    protected function configureServiceManager(ServiceManager $services)
//    {
//        $services->setAllowOverride(true);
//
//        $services->setService('config', $this->updateConfig($services->get('config')));
//        $services->setService(ProductTable::class, $this->mockProductTable()->reveal());
//
//        $services->setAllowOverride(false);
//    }
//
//    protected function updateConfig($config)
//    {
//        $config['db'] = [];
//        return $config;
//    }
//
//    protected function mockProductTable()
//    {
//        $this->ProductTable = $this->prophesize(ProductTable::class);
//        return $this->ProductTable;
//    }

    public function testGetListCanBeAccessed()
    {
//        $this->ProductTable->fetchProducts()->willReturn();
//        $this->ProductTable
//            ->fetchProducts()
//            ->shouldBeCalled();

        $this->dispatch('/product', 'GET');
        $this->assertResponseStatusCode(200);
//        $responseJson = json_decode($this->getResponse()->getContent());
        $this->assertModuleName('Product');
        $this->assertControllerName(ProductController::class);
        $this->assertControllerClass('ProductController');
        $this->assertMatchedRouteName('product');
    }

//    public function testGetCanBeAccessed()
//    {
//        $this->dispatch('/product/1');
//        $this->assertResponseStatusCode(200);
//        $this->assertModuleName('Product');
//        $this->assertControllerName(ProductController::class);
//        $this->assertControllerClass('ProductController');
//        $this->assertMatchedRouteName('product');
//    }
}