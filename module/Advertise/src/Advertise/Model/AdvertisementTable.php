<?php
namespace Advertise\Model;
use Advertise\Model\Advertisement;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

class AdvertisementTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function save(Advertisement $ads)
    {
        $data = array(
            'email' => $ads->email,
            'productId' => $ads->productId,
            'productName' => $this->getProductNameById($ads->productId),
            'postAddress' => $ads->postAddress,
            'addrLatitude' => $ads->addrLatitude,
            'addrLongitude' => $ads->addrLongitude,
            'postDate' => $ads->postDate,
            'status' => Advertisement::STATUS_NORMAL,
        );
        
        $adsId = (int)$ads->adsId;
        if ($adsId == 0) {
            if ($this->tableGateway->insert($data)) {
                return $this->tableGateway->getLastInsertValue();
            }
        } else {
            $retstat = $this->tableGateway->update($data, array('adsId' => $adsId));
            if ($retstat)
                return $retstat;
        }
    }
    
    public function fetchById($adsId)
    {
        if (!empty($adsId)) {
            $select = $this->tableGateway
                            ->getSql()->select();
            $select->where(array(
                'adsId' => (int)$adsId
            ));
            $results = $this->tableGateway->selectWith($select);
            if ($results->count() == 1) {
                return $results->current();
            }
        }
    }
    
    public function fetchAll(Select $select = null)
    {
        if (null === $select) {
            $select = $this->tableGateway->getSql()->select();
        }
        else {
            $select->from($this->tableGateway->getTable());
        }
        $select->where(array("status" => Advertisement::STATUS_NORMAL));
        
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
        
        /*
        // create a new result set based on the advertisement entity
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Advertisement());
        // create a new pagination adapter object
        
        $paginatorAdapter = new DbSelect(
            // our configured select object
            $select,
            // the adapter to run it against
            $this->tableGateway->getAdapter(),
            // the result set to hydrate
            $resultSetPrototype
        );
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
        */
    }
    public function updateStatusByEmail($emailAddr)
    {
        $retstat = $this->tableGateway->update(array('status' => Advertisement::STATUS_BLOCKED), array('email' => $emailAddr));
       
        return $retstat;
    }
    public function getTable()
    {
        return $this->tableGateway->getTable();
    }
    
    protected function getProductNameById($productId)
    {
        $dbAdapter = $this->tableGateway->getAdapter();
        $select = 'SELECT product_name FROM product where product_id = '.$productId;
        $statement = $dbAdapter->query($select);
        $result = $statement->execute();
        $res = $result->current();
        
        return $res['product_name'];
    }
    
}

?>