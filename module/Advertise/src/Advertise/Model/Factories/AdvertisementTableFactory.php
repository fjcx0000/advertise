<?php
namespace Advertise\Model\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Advertise\Model\AdvertisementTable;


class AdvertisementTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableGateway = $serviceLocator->get('Advertise\Model\AdvertisementTableGateway');
        
        $table = new AdvertisementTable($tableGateway);

        return $table;
    }
}

?>