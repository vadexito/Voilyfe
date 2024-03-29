<?php

class Application_Controller_Plugin_MobileInit extends Zend_Controller_Plugin_Abstract
{
    const DEVICE_TYPE_DESKTOP = 1;
    const DEVICE_TYPE_MOBILE = 2;
    const DEVICE_TYPE_TABLET = 3;
    
    /**
     * 
     * @var integer / NULL (no forcing type of device, 
     * for production should be on NULL) 
     */
    protected $_deviceChoice = NULL;
    
    protected $_layout;
    
    protected $_deviceType;
    
    protected $_session;
    
    public function __construct()
    {
        $this->_session = $a = new Zend_Session_Namespace('mylife_device_info');
        $this->_initDeviceChoice();
        
    }
    
    public function dispatchLoopStartup(
            Zend_Controller_Request_Abstract $request)
    {
        $this->_layout = Zend_Layout::getMvcInstance();
        
        $this->_initDeviceInfo();
        $deviceType = $this->_deviceType;
        
        $this->_initCss($deviceType);
        $this->_initJs($deviceType);
        $this->_initMeta($deviceType);
    }
    
    
    protected function _initDeviceChoice()
    {
        //if there a testing parameter (testing phase)
        if (isset(Zend_Registry::get('config')->plugin->mobileInit->testType))
        {
            $this->_deviceChoice = (int)Zend_Registry::get('config')->plugin->mobileInit->testType;
            $this->_session->deviceType = $this->_deviceChoice;
            $this->_deviceType = $this->_deviceChoice;
        }
        else if($this->_session->deviceChoice)
        {//var_dump($this->_session->deviceChoice);die;
            $this->_deviceChoice = (int)$this->_session->deviceChoice;
            $this->_session->deviceType = $this->_deviceChoice;
            $this->_deviceType = $this->_deviceChoice;
        }
    }
    
    
    /**
     *
     * into registry capabilities array
     * parameter 
     * ['is_tablet']
     * ['dual_orientation']
     * ['xhtml_file_upload']
     * ['is_wireless_device']
     *  
     */
    protected function _initDeviceInfo()
    {
        if (!Zend_Registry::isRegistered('capabilities'))
        {
            $frontController = Zend_Controller_Front::getInstance();
            $bootstrap = $frontController->getParam('bootstrap');
            $userAgent = $bootstrap->getResource('useragent');
            $config = $userAgent->getConfig();

            $capabilities = 
                Zend_Http_UserAgent_Features_Adapter_TeraWurfl::getFromRequest(
                    $_SERVER,
                    $config
            );
            Zend_Registry::set('capabilities', $capabilities);
        }   
        
        if (!isset($this->_session->deviceType))
        {
            if (($this->_layout->getView()->userAgent() &&
            $this->_layout->getView()->userAgent()->getDevice()->getType() === 'mobile'))
            {
                $this->_session->deviceType = self::DEVICE_TYPE_MOBILE;
            }
            else
            {
                $this->_session->deviceType = self::DEVICE_TYPE_DESKTOP;
            }
        }
        $this->_deviceType = $this->_session->deviceType;
    }
    
    protected function _initCss($deviceType)
    { 
        $view = $this->_layout->getView();
        
        //init library files
        if ($deviceType === self::DEVICE_TYPE_MOBILE)
        {
            //using jquery mobile
            $view->headLink()->prependStylesheet('/application/css/global.mobile.css');
            $view->headLink()->prependStylesheet('/css/lib/jquery_mobile/jquery.mobile.mylife_theme.css');
            $view->headLink()->prependStylesheet('/css/lib/jquery_mobile/jquery.mobile.structure.css');
        }
        else
        {
            //using twitter bootstrap
            $view->headLink()->prependStylesheet('/application/css/global.css');
            $view->headLink()->prependStylesheet('/css/lib/bootstrap/bootstrap-responsive.css');
            $view->headLink()->prependStylesheet('/css/lib/bootstrap/bootstrap.css');
            $view->headLink()->appendStylesheet('/css/lib/jquery-ui/jquery-ui.custom.css');
        }
    }
    
    protected function _initJs($deviceType)
    {
        $view = $this->_layout->getView();
        
        //init library files
        if ($deviceType === self::DEVICE_TYPE_MOBILE)
        {
            $view->headScript()->offsetSetFile(5,$view->minifyJs('/js/lib/jquery.js'));
            $view->headScript()->offsetSetFile(20,$view->minifyJs('/js/lib/jquery_mobile/jquery.mobile.js'));
            $view->inlineScript()->offsetSetFile(500,$view->minifyJs('/application/js/globalinline.mobile.js'));
        }
        else
        {
            $view->inlineScript()->offsetSetFile(5,$view->minifyJs('/js/lib/jquery.js'));
            $view->inlineScript()->offsetSetFile(20,$view->minifyJs('/js/lib/jquery-ui/jquery-ui.custom.js'));
            $view->inlineScript()->offsetSetFile(30,$view->minifyJs('/js/lib/bootstrap.js'));
            $view->inlineScript()->offsetSetFile(40,$view->minifyJs('/js/lib/underscore.js'));
            $view->inlineScript()->offsetSetFile(50,$view->minifyJs('/js/lib/backbone.js'));
            
        }
    }
    
    protected function _initMeta($deviceType)
    {
        $view = $this->_layout->getView();
        
        //init library files
        if ($deviceType === self::DEVICE_TYPE_MOBILE)
        {
            $view->headMeta()->appendName('viewport','width=device-width, initial-scale=1');
            $view->headMeta()->appendName('apple-mobile-web-app-capable','yes');
        }
    }
    
}
