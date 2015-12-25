<?php
namespace Advertise\Forms;

use Zend\Validator\AbstractValidator;

class UsernameValidator extends AbstractValidator
{
    const STARTCHAR = 'startchar';
    protected $messageTemplates = array(
        self::STARTCHAR => "Field should start with Charactor",
    );

    public function isValid($value)
    {
       // echo "start username validate ".$value;
        $this->setValue($value);
        $isValid = true;
        
        if (!preg_match('/^[A-Za-z][a-zA-Z0-9]+/',$value)) {
            $this->error(self::STARTCHAR);
            $isValid = false;
        // var_dump($this->getMessages());
        }
        return $isValid;
    }
}

?>