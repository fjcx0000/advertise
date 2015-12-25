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

class AdvertiseInputFilter extends InputFilter
{
    protected $_requiredFields = array(
        "email",
        "productId",
        "postAddress",
        "postDate",
    );
    protected $_optionalFields = array(
        "adsId",
        "status",
    );

    function __construct()
    {
        // add the fields to the input filter
        $this->_addRequiredFields()
            ->_addOptionalFields();
    }
    
    protected function _addOptionalFields()
    {
        foreach ($this->_optionalFields as $fieldName) {
            $input = new Input($fieldName);
            $input->setRequired(true)
                ->setAllowEmpty(true)
                ->setFilterChain($this->_getStandardFilter());
            switch ($fieldName) {
                case ("adsId"):
                    $input->getValidatorChain()
                        ->attach(
                            new Validator\Digits(
                                array(
                                    'messageTemplates' => array(
                                        Validator\Digits::NOT_DIGITS => 'The value supplied is not a valid number',
                                        Validator\Digits::STRING_EMPTY => 'A value must be supplied',
                                        Validator\Digits::INVALID => 'The value supplied is not a valid number',
                                    )
                                )
                            )
                        );
                    break;
                case ("status"):
                    $input->getValidatorChain()
                        ->attach(
                            new InArray(
                                array(
                                    // 0: Normal; 1: Blocked; 2: Abandoned
                                    'haystack' => array('0','1','2'),
                                    'strict' => InArray::COMPARE_NOT_STRICT
                                )
                            )
                        );
                     break;
            }
            $this->add($input);
        }
        return $this;
    }
    protected function _addRequiredFields()
    {
        foreach ($this->_requiredFields as $fieldName) {
            $input = new Input($fieldName);
            $input->setRequired(true)
                ->setAllowEmpty(false)
                ->setFilterChain($this->_getStandardFilter());
            switch ($fieldName) {
                case ("email"):
                    $input->getValidatorChain()
                        ->attach(new Validator\EmailAddress());
                    break;
                case ("productId"):
                    $input->getValidatorChain()
                    ->attach(
                        new Validator\Digits(
                            array(
                                'messageTemplates' => array(
                                    Validator\Digits::NOT_DIGITS => 'The value supplied is not a valid number',
                                    Validator\Digits::STRING_EMPTY => 'A value must be supplied',
                                    Validator\Digits::INVALID => 'The value supplied is not a valid number',
                                )
                            )
                        )
                    );
                    break;
                case ("postAddress"):
                    $input->getValidatorChain()
                        ->attach(
                            new Validator\StringLength(
                                array('min' => 4, 'max' => 100)
                            )
                        );
                    break;
                case ("postDate"):
                    $input->getValidatorChain()
                        ->attach(
                            new Validator\Date(array())
                        );
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
}

?>