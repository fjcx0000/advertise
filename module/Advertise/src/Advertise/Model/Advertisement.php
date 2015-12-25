<?php
namespace Advertise\Model;

class Advertisement
{
    public $adsId;
    public $email;
    public $productId;
    public $productName;
    public $postAddress;
    public $addrLatitude;
    public $addrLongitude;
    public $postDate;
    public $status;
    
    CONST STATUS_NORMAL = 1;
    CONST STATUS_BLOCKED = 2;
    CONST STATUS_DISCONTINUED = 3;
    
    public  function exchangeArray($data)
    {
        $this->adsId = (isset($data['adsId'])) ? $data['adsId'] : NULL;
        $this->email = (isset($data['email'])) ? $data['email'] : NULL;
        $this->productId = (isset($data['productId'])) ? $data['productId'] : NULL;
        $this->productName = (isset($data['productName'])) ? $data['productName'] : NULL;
        $this->postAddress = (isset($data['postAddress'])) ? $data['postAddress'] : NULL;
        $this->addrLatitude = (isset($data['addrLatitude'])) ? $data['addrLatitude'] : NULL;
        $this->addrLongitude = (isset($data['addrLongitude'])) ? $data['addrLongitude'] : NULL;
        $this->postDate = (isset($data['postDate'])) ? $data['postDate'] : NULL;
        //$this->status = (isset($data['status'])) ? $data['status'] : NULL;
        $this->status = (isset($data['status'])) ? $data['status'] : Advertisement::STATUS_NORMAL;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}

?>