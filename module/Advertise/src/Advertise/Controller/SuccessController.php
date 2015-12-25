<?php
namespace Advertise\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

/**
 * SuccessController
 *
 * @author
 *
 * @version
 *
 */
class SuccessController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        // TODO Auto-generated SuccessController::indexAction() default action
        $authService = $this->getServiceLocator()->get('AuthService');
        
        if (!$authService->getStorage()->getSessionManager()->getSaveHandler()
            ->read($authService->getStorage()->getSessionId())) {
                return $this->redirect()->toRoute('advertise/login', array());
            }
        $ads_session = new Container('advertise');
        $previousUrl = $ads_session->pre_url;
        return new ViewModel(array(
            'previousUrl' => $previousUrl,
        ));
    }
}