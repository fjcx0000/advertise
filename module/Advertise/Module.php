<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Advertise for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Advertise;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\StaticEventManager;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Advertise\Model\AdvertisementTableGateway' => 'Advertise\Model\Factories\AdvertisementTableGatewayFactory',
                'Advertise\Model\AdvertisementTable' => 'Advertise\Model\Factories\AdvertisementTableFactory',
            ),
        );
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $app = $e->getParam("application");
        $app->getEventManager()->attach('dispatch', array($this, 'setLayout'));
        
        //Register log event
        $log = $this->getLogger($e);
        $events = StaticEventManager::getInstance();
        $events->attach('*', 'log', function($event) use ($log){
            $target = get_class($event->getTarget());
            $message = $event->getParam('message', 'No message provided');
            $priority = (int) $event->getParam('priority', \Zend\Log\Logger::INFO);
            $message = sprintf("%s: %s", $target, $message);
            $log->log($priority, $message);
        });
    }
    
    public function setLayout($e)
    {
        $matches = $e->getRouteMatch();
        $controller = $matches->getParam('controller');
        if (false === strpos($controller, __NAMESPACE__)) {
            //not a controller from this module
            return ;
        }
        //set the layout template
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('content/layout');
    }
    
    private function getLogger($e)
    {
        $log = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream(__DIR__ . "/../../data/log/advertise." . date('Ymd'));
        $log->addWriter($writer);
        return $log;
    }
}
