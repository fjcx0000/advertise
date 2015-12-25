<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Advertise for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Advertise\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Advertise\Model\AdvertisementTable;
use Advertise\Model\Advertisement;
use Advertise\Forms\AdvertiseForm;
use Zend\Db\Sql\Select;

use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter\HtmlEntities;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\Filter\FilterChain;
use Zend\Validator;

use Doctrine\ORM\EntityManager;
use Advertise\Entity\ADEntity;

use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Authentication\AuthenticationService;
use Advertise\Model\User;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Session\Container;
use Advertise\Forms\RegisterForm;
use Advertise\Entity\UserEntity;
use Zend\View\Model\JsonModel;

class AdvertiseController extends AbstractActionController
{
    const DEFAULT_ROUTE = 'advertise';
    
    protected $advertisementTable;
    protected $authService;
    protected $form;
    
    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;
    
    public function __construct(AdvertisementTable $advertisementTable,AuthenticationService $authService)
    {
        $this->advertisementTable = $advertisementTable;
        $this->authService = $authService;
    }
    
    public function loginAction()
    {
        if ($this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
                //redirect to success
                return $this->redirect()->toRoute('success');
        }
        $form = $this->getForm();
        $viewModel = new ViewModel();
        $viewModel->setVariable('error', '');
        $this->authenticate($form, $viewModel);
        $viewModel->setVariable("form", $form);
        $viewModel->setVariable('error', '');
        $this->authenticate($form, $viewModel);
        
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }
    
    public function logoutAction()
    {
       /*
        if (!$this->getServiceLocator()
            ->get('authService')->hasIdentity()) {
       */
        $ads_session = new Container('advertise');
        $ads_session->pre_url = $this->url()->fromRoute('advertise');
        
        if (!$this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
           return $this->redirect()->toRoute('advertise/login', array());
        }
        $this->getServiceLocator()->get('authService')->getStorage()->clear();
        $this->redirect()->toRoute('advertise/login', array());
    }
    
    public function indexAction()
    {
        if (!$this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
                $ads_session = new Container('advertise');
                $ads_session->pre_url = $this->url()->fromRoute('advertise');
                return $this->redirect()->toRoute('advertise/login', array());
        }
        
        $select = new Select();
        $order_by = $this->params()->fromRoute('order_by')?
            $this->params()->fromRoute('order_by') : 'adsId';
        $order = $this->params()->fromRoute('order')?
            $this->params()->fromRoute('order') : 'ASC';
        $page = $this->params()->fromRoute('page')?
            (int) $this->params()->fromRoute('page') : 1;
        
        $select->order($order_by.' '. $order);
        $select->from($this->advertisementTable->getTable());
        
        $itemsPerPage = 3;
        //$resultSet = $this->advertisementTable->fetchAll($select);
        //$paginator = new Paginator(new Iterator($resultSet));
        
        //Doctrine
        $resultSet = $this->getEntityManager()->getRepository('Advertise\Entity\ADEntity')->findBy(array('status' => ADEntity::STATUS_NORMAL));
        //var_dump($resultSet);
        
        $paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultSet));
        
        $paginator->setCurrentPageNumber($page)
                    ->setItemCountPerPage($itemsPerPage)
                    ->setPageRange(7);
        
        return new ViewModel(array(
            'page' => $page,
            'paginator' => $paginator,
            'order_by' => $order_by,
            'order' => $order,
        ));
    }
    public function manageAction()
    {
        /*
        $formManager = $this->serviceLocator->get('FormelementManager');
        $form = $formManager->get(
            'Advertise\Forms\AdvertiseForm');
        */
        if (!$this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
                $ads_session = new Container('advertise');
                $ads_session->pre_url = $this->url()->fromRoute('advertise/manage');
                return $this->redirect()->toRoute('advertise/login', array());
            }
            
        $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
        $form = new AdvertiseForm($dbAdapter);
        
        $adsId = (int)$this->params()->fromRoute('id');
        
        if ($this->getRequest()->isGet()) {
            if (!empty($adsId)) {
                /*
                if ($ads = $this->advertisementTable->fetchById($adsId)) {
                    $form->setData($ads->getArrayCopy());
                } else {
                    return $this->redirect()->toRoute(
                        self::DEFAULT_ROUTE,
                        array('action' => 'manage'));
                }
                */
                // Doctrine
                if ($ads = $this->getEntityManager()->getRepository('Advertise\Entity\ADEntity')->find($adsId)) {
                    $form->setData($ads->getArrayCopy());
                } else {
                    return $this->redirect()->toRoute('advertise/manage', array());
                }
            }
        }
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                /*
                $ads = new Advertisement();
                $ads->exchangeArray($form->getData());
                $ads->adsId = $this->advertisementTable->save($ads);
                */
                $ads = new ADEntity();
                $ads->exchangeArray($form->getData());
                $productId = $this->getRequest()->getPost("productId");
                $product = $this->getEntityManager()->getRepository("Advertise\Entity\ProductEntity")->find($productId);
                $ads->productName =  $product->productName;
                $this->getEntityManager()->persist($ads);
                $this->getEntityManager()->flush();
                
                $message = "Advertisement Entity persisted successfully, email=["
                     . $ads->email . "] product=["
                     . $ads->productName . "] address=["
                     . $ads->postAddress . "]";
                //$this->log($message);
                //$this->getServiceLocator()->get('logger')->info($message);
                $this->getEventManager()->trigger('log', $this, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $message,
                ));
                return $this->redirect()->toRoute(self::DEFAULT_ROUTE,array());
            }
        }
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    public function markmapAction()
    {
        if (!$this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
                $ads_session = new Container('advertise');
                $ads_session->pre_url = $this->url()->fromRoute('advertise/markmap');
                return $this->redirect()->toRoute('advertise/login', array());
            }
            
        $resultSet = $this->advertisementTable->fetchAll();
        
        return new ViewModel(array(
            'list' => $resultSet,
        ));
    }
    
    public function blockemailAction()
    {
        if (!$this->authService->getStorage()->getSessionManager()
            ->getSaveHandler()
            ->read($this->authService->getStorage()->getSessionId())) {
                $ads_session = new Container('advertise');
                $ads_session->pre_url = $this->url()->fromRoute('advertise/blockemail');
                return $this->redirect()->toRoute('advertise/login', array());
            }
        $input = new Input('email');
        $input->setRequired(true)
        ->setAllowEmpty(false);
        $baseInputFilterChain = new FilterChain();
         $baseInputFilterChain
            ->attach(new HtmlEntities())
            ->attach(new StringTrim())
            ->attach(new StripNewlines())
            ->attach(new StripTags());
         $input->setFilterChain($baseInputFilterChain);
         $input->getValidatorChain()
            ->attach(new Validator\EmailAddress());
               
        $form = new Form('Email');
        $form->setAttribute('method', 'post')
        ->setAttribute('class', 'form-horizontal')
        //           ->setAttribute('action', '/advertise/manage')
        ->setAttribute('enctype', 'multipart/form-data')
        ->setValidationGroup(array(
            'email',
        ));
        $inputFilter = new InputFilter();
        $inputFilter->add($input);
        $form->setInputFilter($inputFilter);
        $form->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Gumtree Accounts/Email Address'
            ),
            'attributes' => array(
                'id' => 'emailAddr',
                'class' => 'form-control',
                'placeholder' => 'gumtree accounts'
            )
        ));
        $form->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'options' => array(
                'label' => 'Submit'
            ),
            'attributes' => array(
                'class' => 'btn btn-default'
            )
        ));
        
        $form->get('submit')->setValue('Submit');
        
       
        if ($this->getRequest()->isPost()) {
            $emailAddr = $this->getRequest()->getPost('email');
            $form->setData($this->getRequest()->getPost());
        
            if ($form->isValid()) {
                
                //$res = $this->advertisementTable->updateStatusByEmail($emailAddr);
                $query = $this->getEntityManager()->createQuery("update Advertise\Entity\ADEntity t set t.status = ".ADEntity::STATUS_BLOCKED.
                            " where t.email = '".$emailAddr."' and t.status = ".ADEntity::STATUS_NORMAL);
                $res = $query->execute();
                
                $message = sprintf("[%d] ads losted, I am sorry.",$res);
                $this->emailNotify($message, $emailAddr);
                $view = new ViewModel(array(
                    'title' => "Gumtree Block Email",
                    'message' => $message
                ));
                $view->setTemplate("advertise/result");
                return $view;
            }
        }
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    public function registerAction()
    {
        
        $form = new RegisterForm($this->getRequest()->getBaseUrl()."/advertise/captcha/");
        
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            $form->getInputFilter()->addPasswordIdenticalValid($this->getRequest()->getPost('password'));
        
            if ($form->isValid()) {
                
                $user = new UserEntity();
                $user->exchangeArray($form->getData());
                $user->status = UserEntity::STATUS_UNACITVE;
                $user->password = md5($user->password);
                $user->activeCode = md5($user->username . Date("YmdHis") . rand(100,999));
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
                
                $url = $this->url()->fromRoute("advertise/activate",array(
                    "username" => $user->username,
                    "activatecode" => $user->activeCode,
                ), array("force_canonical" => true));
                $this->sendEmail($user->email, "activation", "Please acitvation your account timely", array(
                    "username" => $user->username,
                    "activatelink" => $url,
                ));
        /*
                $message = "Advertisement Entity persisted successfully, email=["
                    . $ads->email . "] product=["
                        . $ads->productName . "] address=["
                            . $ads->postAddress . "]";
                //$this->log($message);
                //$this->getServiceLocator()->get('logger')->info($message);
                $this->getEventManager()->trigger('log', $this, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $message,
                   
                ));
        */
                $viewModel = new ViewModel(array(
                    'username' => $user->username,
                ));
                $viewModel->setTemplate("advertise/advertise/register_success.phtml");
                return $viewModel;
            }
        }
        
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    public function activateAction()
    {
        $message = "Sorry, activation failed...";
        if ($this->getRequest()->isGet()) {
            $username = $this->params()->fromRoute("username");
            $activateCode = $this->params()->fromRoute("activatecode");
            $user = $this->getEntityManager()->getRepository("Advertise\Entity\UserEntity")->findOneBy(array(
                'username' => $username,
            ));
            if ($user == NULL)
            {
                $message = "Sorry, ".$username." doesn't exist.";
            } else if ($user->status == UserEntity::STATUS_ACTIVE) {
                $message = $username." have been activated already.";
            } else if ($user->activeCode != $activateCode) {
                $message = "Activate Code is wrong";
            } else {
                $user->status = UserEntity::STATUS_ACTIVE;
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
                $message = $username." is activated successfully.";
            }
        }
        return new ViewModel(array(
            'message' => $message
        ));
    }
    
    public function captchaAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', "image/png");
        
        $id = $this->params('id', false);
        
        if ($id) {
        
            $image = __DIR__ . "/../../../../../data/captcha/" . $id;
        
            if (file_exists($image) !== false) {
                $imagegetcontent = @file_get_contents($image);
        
                $response->setStatusCode(200);
                $response->setContent($imagegetcontent);
        
                if (file_exists($image) == true) {
                    unlink($image);
                }
            }
        
        }
        return $response;
    }
    
    public function validateAction()
    {
        $valid = false;
    
        if ($this->getRequest()->isPost()) {
            $valid = true;
            if ($username = $this->getRequest()->getPost('username')) {
                $user = $this->getEntityManager()->getRepository("Advertise\Entity\UserEntity")->findOneBy(array(
                    'username' => $username,
                ));
                if ($user) {
                    $valid = false;
                }
            }
        }
        
        return new JsonModel(array(
            'valid' => $valid,
        ));
    }
    
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
    public function emailNotify($message,$emailAddress)
    {
        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . "/../../../view/mails/blocknotify.phtml"
        ));
        $view->setResolver($resolver);
        $viewModel = new ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables(array(
            'emailAddr' => $emailAddress,
            'message' => $message,
        ));
        $bodyPart = new \Zend\Mime\Message();
        $bodyMessage = new \Zend\Mime\Part($view->render($viewModel));
        $bodyMessage->type = 'text/html';
        $bodyPart->setParts(array($bodyMessage));
        
        $mail = new Mail\Message();
        $mail->setBody($bodyPart)
            ->setFrom('heqs20151015@gmail.com')
            ->addTo($emailAddress)
            ->setSubject('Email Blocked Notice');
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => 'smtp.gmail.com',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'connection_class' => 'plain',
            'connection_config' => array(
                'username' => 'heqs20151015@gmail.com',
                'password' => 'heqs326688',
                'ssl' => 'tls',
            ),
        ));
        $transport->setOptions($options);
        $transport->send($mail);
    }
    
    public function sendEmail($emailAddress,$templateName,$subjectName, $params)
    {
        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $config = $this->getServiceLocator()->get("config");
        $resolver->setMap($config['email_template']);
        $view->setResolver($resolver);
        $viewModel = new ViewModel();
        $viewModel->setTemplate($templateName)->setVariables($params);
        $bodyPart = new \Zend\Mime\Message();
        $bodyMessage = new \Zend\Mime\Part($view->render($viewModel));
        $bodyMessage->type = 'text/html';
        $bodyPart->setParts(array($bodyMessage));
    
        $mail = new Mail\Message();
        $mail->setBody($bodyPart)
        ->setFrom('heqs20151015@gmail.com')
        ->addTo($emailAddress)
        ->setSubject($subjectName);
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => 'smtp.gmail.com',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'connection_class' => 'plain',
            'connection_config' => array(
                'username' => 'heqs20151015@gmail.com',
                'password' => 'heqs326688',
                'ssl' => 'tls',
            ),
        ));
        $transport->setOptions($options);
        $transport->send($mail);
    }
    
    public function authenticate($form, $viewModel)
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $dataForm = $form->getData();

                $this->authService->getAdapter()
                                        ->setIdentity($dataForm['username'])
                                        ->setCredential($dataForm['password']);
                $result = $this->authService->authenticate();
                if ($result->isValid()) {
                    //authentication success
                    $resultRow = $this->authService->getAdapter()->getResultRowObject();
                    $this->authService->getStorage()->write(
                        array('id'      =>  $resultRow->id,
                                'username' => $dataForm['username'],
                                'ip_address' => $this->getRequest()->getServer('REMOTE_ADDR'),
                                'user_agent' => $request->getServer('HTTP_USER_AGENT')
                    ));
                    return $this->redirect()->toRoute('success', array('action' => 'index'));
                } else {
                    $viewModel->setVariable('error', 'Login Error');
                }
            }
        }
    }
    
    public function log($message)
    {
        $writer = new \Zend\Log\Writer\Stream(__DIR__ . "/../../../../../data/log/advertise." . date('Ymd'));
        
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        $logger->log(\Zend\Log\Logger::INFO, $message);
    }
    public function getForm()
    {
        if (! $this->form) {
            $user = new User();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }
        return $this->form;
    }
}
