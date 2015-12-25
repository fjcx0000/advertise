<?php
namespace Advertise\Forms;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class AdvertiseForm extends Form
{
    protected $adapter;

    function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        
        parent::__construct('AdvertiseForm');
        
        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'form-horizontal')
 //           ->setAttribute('action', '/advertise/manage')
            ->setAttribute('enctype', 'multipart/form-data')
            ->setValidationGroup(array(
                'adsId',
                'email',
                'productId',
                'postAddress',
                'addrLatitude',
                'addrLongitude',
                'postDate',
            ));
        $this->setInputFilter(new AdvertiseInputFilter());
        
        //Add form elements
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'adsId',
            'options' => array(),
            'attributes' => array()
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Gumtree Accounts/Email Address'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'gumtree accounts'
            )
        ));
/*        
        $this->add(array(
            'type' => 'Zend\Form\Element\Number',
            'name' => 'productId',
            'options' => array(
                'label' => 'product id'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'product id',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'productName',
            'options' => array(
                'label' => 'product name'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'product name',
            )
        ));
        */
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'productId',
            'options' => array(
                'label' => 'Product',
                'value_options' => $this->getProductsForSelect(),
                'empty_option' => '--- please choose ---'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'postAddress',
            'options' => array(
                'label' => 'post address'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'post address',
                'id' => 'postAddress',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'addrLatitude',
            'options' => array(),
            'attributes' => array(
                'id' => 'addrLatitude',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'addrLongitude',
            'options' => array(),
            'attributes' => array(
                'id' => 'addrLongitude',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'postDate',
            'options' => array(
                'label' => 'post date'
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'post date',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'options' => array(
                'label' => 'Advertise Status',
                'value_options' => array(
                    '0' => 'Normal',
                    '1' => 'Blocked',
                    '2' => 'Abandoned',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'value' => '0'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'options' => array(
                'label' => 'Save'
            ),
            'attributes' => array(
                'class' => 'btn btn-default'
            )
        ));
        
        $this->get('submit')->setValue('Save');
    }
    
    public function getProductsForSelect()
    {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT product_id, product_name FROM product ORDER BY product_name';
        $statement = $dbAdapter->query($sql);
        $results = $statement->execute();
        
        $selectData = array();
        
        foreach($results as $res) {
            $selectData[$res['product_id']] = $res['product_name'];
        }
        
        return $selectData;
    }
}

?>