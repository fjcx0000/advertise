<?php
namespace Advertise\Forms;

use Zend\Form\Form,
    Zend\Form\Element\Captcha,
    Zend\Captcha\Image as CaptchaImage;


class RegisterForm extends Form
{

    function __construct($urlcaptcha = null)
    {
        parent::__construct('RegisterForm');
        
        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'form-horizontal')
            ->setAttribute('enctype', 'multipart/form-data')
            ->setValidationGroup(array(
                'username',
                'password',
                'repeatPassword',
                'email',
                'captcha'
            ));
        $this->setInputFilter(new RegisterInputFilter());
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'username',
            'options' => array(
                'label' => 'Username',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'username',
                'id' => 'username',
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email Address',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'active email address'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'repeatPassword',
            'options' => array(
                'label' => 'Repeat Password',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'options' => array(
                'label' => 'Registry'
            ),
            'arrtributes' => array(
                'class' => 'btn btn-primary'
            )
        ));
        
        $dirdata = __DIR__ . "/../../../../../";
        $captchaImage = new CaptchaImage(  array(
            'font' => $dirdata . '/public/fonts/arial.ttf',
            'width' => 250,
            'height' => 100,
            'dotNoiseLevel' => 40,
            'lineNoiseLevel' => 3)
        );
        
        $captchaImage->setImgDir($dirdata.'/data/captcha');
        $captchaImage->setImgUrl($urlcaptcha);
        
        //add captcha element...
        $this->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => array(
                'label' => 'Please verify you are human',
                'captcha' => $captchaImage,
            ),
        ));
        
        $this->get('submit')->setValue('Registry');
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'reset',
            'arrtributes' => array(
                'class' => 'btn btn-warning',
            )
        ));
        $this->get('reset')->setValue('Reset');
        
    }
}

?>