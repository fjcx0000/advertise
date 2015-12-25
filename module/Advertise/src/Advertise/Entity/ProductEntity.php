<?php
namespace Advertise\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A Product.
 *
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @property int $product_id
 * @property string $product_name
 * @property smallint $status
 */
class ProductEntity
{
    CONST STATUS_NORMAL = 1;
    CONST STATUS_BLOCKED = 2;
    CONST STATUS_DISCONTINUED = 3;

    /**
     * @ORM\Id
     * @ORM\Column(name="product_id", type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $productId;
    /**
     * @ORM\Column(name="product_name", type="string")
     */
    protected $productName;
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
        $this->productId = (isset($data['productId'])) ? $data['productId'] : NULL;
        $this->productName = (isset($data['productName'])) ? $data['productName'] : NULL;
        $this->status = (isset($data['status'])) ? $data['status'] : ProductEntity::STATUS_NORMAL;
    }
}

?>