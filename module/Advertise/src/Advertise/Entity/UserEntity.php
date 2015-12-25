<?php
namespace Advertise\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User entity
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property int $status
 * @property string $activeCode
 */
class UserEntity
{
    CONST STATUS_ACTIVE = 1;
    CONST STATUS_UNACITVE = 0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $username;
    /**
     * @ORM\Column(type="string")
     */
    protected $password;
    /**
     * @ORM\Column(type="string")
     */
    protected $email;
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;
    /**
     * @ORM\Column(type="string")
     */
    protected $activeCode;
    
    /**
     * Magic getter to expose protected properties.
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }
    
    /**
     * Magic setter to save protected properties.
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    /**
     * Convert the object to an array.
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    /**
     * Populate from an array.
     * @param array $data
     */
    public function exchangeArray($data = array())
    {
        $this->username = (isset($data['username'])) ? $data['username'] : NULL;
        $this->password = (isset($data['password'])) ? $data['password'] : NULL;
        $this->email = (isset($data['email'])) ? $data['email'] : NULL;
        $this->status = (isset($data['status'])) ? $data['status'] : UserEntity::STATUS_UNACITVE;
        $this->activeCode = (isset($data['activeCode'])) ? $data['activeCode'] : NULL;
    }
}

?>