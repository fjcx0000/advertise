<?php
namespace Advertise\Controller\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Advertise\Controller\AdvertiseController;

class AdvertiseControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        if ($sm->has('Advertise\Model\AdvertisementTable')) {
            $this->advertisementTable = $sm->get('Advertise\Model\AdvertisementTable');
        }
        $authService = $sm->get('AuthService');
        $controller = new AdvertiseController($this->advertisementTable,$authService);
        
        return $controller;
    }
}

?>