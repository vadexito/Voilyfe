<?php


class Application_Controller_Action_Helper_DispatchToMobileFile extends Zend_Controller_Action_Helper_Abstract
{

    
    
    public function direct()
    {
        $this->getActionController()->view->addScriptPath('../application/layouts/scripts/');
        
        $viewRenderer = 
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $session = new Zend_Session_Namespace('mylife_device_info');
        
        if ( $session->deviceType)
        {
            $deviceType = $session->deviceType;
            $controller = $this->getRequest()->getControllerName();
            $action = $this->getRequest()->getActionName();
            $viewName = $action.'-mobile';

            if ($deviceType === 
                    Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE &&
                    $viewRenderer->view->scriptFileExists($viewName,$controller))
            {
                $viewRenderer->setRender($viewName);
            }
        }
    }

}

