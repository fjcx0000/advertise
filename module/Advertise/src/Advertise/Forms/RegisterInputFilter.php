<?php
namespace Advertise\Forms;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter\HtmlEntities;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\Filter\FilterChain;
use Zend\Validator;
use Zend\Validator\InArray;
use Zend\Validator\Identical;

class RegisterInputFilter extends InputFilter
{
    protected $_requiredFields = array(
        "username",
        "email",
        "password",
        "status",
    );

    function __construct()
    {
        $this->_addRequiredFields();
    }
    protected function _addRequiredFields()
    {
        foreach($this->_requiredFields as $fieldName) {
            $input = new Input($fieldName);
            $input->setRequired(true)
                ->setAllowEmpty(false)
                ->setFilterChain($this->_getStandardFilter());
            switch ($fieldName) {
                case ("username"):
                    $input->getValidatorChain()
                        ->attach(
                            new Validator\StringLength(
                                array('min'=> 6, 'max'=> 100)
                            ))
                        ->attach( new UsernameValidator() );
                    break;
                case ("email"):
                    $input->getValidatorChain()
                        ->attach(new Validator\EmailAddress());
                    break;
                case ("password"):
                        $input->getValidatorChain()
                        ->attach(
                        new Validator\StringLength(
                        array('min'=> 6, 'max'=> 20)
                        ))
                        ->attach( new UsernameValidator() );
                        break;
                case ("status"):
                    $input->getValidatorChain()
                        ->attach(new InArray(array(
                            'haystack' => array('0','1'),
                            'strict' => InArray::COMPARE_NOT_STRICT,
                        )));
                    break;
            }
            $this->add($input);
        }
        return $this;
    }
    protected function _getStandardFilter()
    {
        $baseInputFilterChain = new FilterChain();
        $baseInputFilterChain->attach(new HtmlEntities())
            ->attach(new StringTrim())
            ->attach(new StripNewlines())
            ->attach(new StripTags());
        return $baseInputFilterChain;
    }
    public function addPasswordIdenticalValid($password)
    {
        $input = new Input("repeatPassword");
        $input->getValidatorChain()
            ->attach( new Identical($password) );
        $this->add($input);
    }
}

?>