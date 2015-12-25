<?php
namespace Advertise\Model\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Advertise\Model\Advertisement;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;


class AdvertisementTableGatewayFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        try {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $hydrator = new ArraySerializable();
        $rowObjectPrototype = new Advertisement();
        $resultSet = new HydratingResultSet($hydrator, $rowObjectPrototype);
        
        $tableGateway =  new TableGateway(
            'advertisement', $dbAdapter,null, $resultSet
        );
        return $tableGateway;
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            echo "$file:$line ERRNO:$code ERROR:$msg";
        }
        
    }
}

?>