<?php
namespace Advertise\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * A Advertisement on Gumtree.
 *
 * @ORM\Entity
 * @ORM\Table(name="advertisement")
 * @property int $adsId
 * @property string $email
 * @property int $productId
 * @property string $productName
 * @property string $postAddress
 * @property float $addrLatitude
 * @property float $addrLongitude
 * @property date $postDate
 * @property smallint $status
 */
class ADEntity implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    CONST STATUS_NORMAL = 1;
    CONST STATUS_BLOCKED = 2;
    CONST STATUS_DISCONTINUED = 3;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $adsId;
    /**
     * @ORM\Column(type="string")
     */
    protected $email;
    /**
     * @ORM\Column(type="integer")
     */
    protected $productId;
    /**
     * @ORM\Column(type="string")
     */
    protected $productName;
    /**
     * @ORM\Column(type="string")
     */
    protected $postAddress;
    /**
     * @ORM\Column(type="float")
     */
    protected $addrLatitude;
    /**
     * @ORM\Column(type="float")
     */
    protected $addrLongitude;
    /**
     * @ORM\Column(type="date")
     */
    protected $postDate;
    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;
    
    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }
    
    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray($data = array())
    {
        $this->adsId = (isset($data['adsId'])) ? $data['adsId'] : NULL;
        $this->email = (isset($data['email'])) ? $data['email'] : NULL;
        $this->productId = (isset($data['productId'])) ? $data['productId'] : NULL;
        $this->productName = (isset($data['productName'])) ? $data['productName'] : NULL;
        $this->postAddress = (isset($data['postAddress'])) ? $data['postAddress'] : NULL;
        $this->addrLatitude = (isset($data['addrLatitude'])) ? $data['addrLatitude'] : NULL;
        $this->addrLongitude = (isset($data['addrLongitude'])) ? $data['addrLongitude'] : NULL;
        $this->postDate = (isset($data['postDate'])) ? new \DateTime($data['postDate']) : NULL;
        $this->status = (isset($data['status'])) ? $data['status'] : ADEntity::STATUS_NORMAL;
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
    
            $inputFilter->add(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'EmailAddress',
                    ),
                ),
            ));
    
            $inputFilter->add(array(
                'name'     => 'productName',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
    
            $this->inputFilter = $inputFilter;
        }
    
        return $this->inputFilter;
    }
    
}

?>