<?php
namespace Advertise\Factory\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Advertise\Storage\AuthStorage;

class AuthStorageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storage = new AuthStorage('advertise');
        $storage->setServiceLocator($serviceLocator);
        $storage->setDbHandler();
        
        return $storage;
    }
}

?>