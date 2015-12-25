<?php
namespace Advertise;

return array(
    'controllers' => array(
        'invokables' => array(
            'Advertise\Controller\Success' => 'Advertise\Controller\SuccessController'
        ),
        'factories' => array(
            'Advertise\Controller\Advertise' => 
                'Advertise\Controller\Factories\AdvertiseControllerFactory'
        ),
    ),
    'router' => array(
        'routes' => array(
            'advertise' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/advertise',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Advertise\Controller',
                        'controller'    => 'Advertise',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action][/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'action'     => '(?!\bpage\b)(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'manage' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/manage[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'manage',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'markmap' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/markmap[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'markmap',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'blockemail' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/blockemail',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'blockemail',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'login' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/login',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'login',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'logout' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/logout',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'logout',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'register' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/register',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'register',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'activate' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/activate/:username/:activatecode',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'activate',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'captcha' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/captcha[/:id]',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'captcha',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'validate' => array(
                        'type'  => 'segment',
                        'options'   => array(
                            'route'     => '/validate',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller' => 'Advertise\Controller\Advertise',
                                'action' => 'validate',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
            'success' => array(
                'type'    => 'Segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/success[/:action]',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Advertise\Controller',
                        'controller'    => 'Success',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Advertise' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'paginator-slide' => __DIR__ . '/../view/layout/slidePaginator.phtml',
            'content/layout'  => __DIR__ . '/../view/layout/layout.phtml',
            'advertise/result' =>  __DIR__ . '/../view/advertise/advertise/result.phtml',
            'mails/blocknotify' => __DIR__ . '/../view/mails/blocknotify.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'AuthStorage' => 'Advertise\Factory\Storage\AuthStorageFactory',
            'AuthService' => 'Advertise\Factory\Storage\AuthenticationServiceFactory',
            'logger' => function ($sm) {
                $log = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream(__DIR__ . "/../../../data/log/advertise." . date('Ymd'));
                $log->addWriter($writer);
                return $log;
            },
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'advertise',
            ),
            array(
                'label' => 'Manage Ads',
                'route' => 'advertise/manage',
            ),
            array(
                'label' => 'Mark Ads on Map',
                'route' => 'advertise/markmap',
            ),
            array(
                'label' => 'Block Email',
                'route' => 'advertise/blockemail',
            ),
            array(
                'label' => 'Logout',
                'route' => 'advertise/logout',
            ),
            array(
                'label' => 'Registry',
                'route' => 'advertise/register',
            ),
        ),
    ),
    //Doctrine Config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
    ),
    'email_template' => array(
        'activation' => __DIR__ . '/../view/mails/activation.phtml',
     ),
);
