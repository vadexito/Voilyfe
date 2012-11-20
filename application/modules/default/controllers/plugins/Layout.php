<?php

class Application_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $_layout;
    
    protected $_suffix = '';
    
    protected $_defaultLayout = 'layout';
    
    protected $_prefixes = [];
    
    public function dispatchLoopStartup (
        Zend_Controller_Request_Abstract $request)
    {
        $this->_setPrefixes($request);
        $this->_setPrefixDevice();
        $this->_layout = Zend_Layout::getMvcInstance();
        
        $this->_initLayout();
        $this->_initJsAndCSS();
    }
    
    protected function _initLayout()
    {
        $layout = $this->_layout;
        
        foreach (array_merge($this->_prefixes,[$this->_defaultLayout]) as $prefix)
        {
            $layoutFile = $layout->getLayoutPath().$prefix.$this->_suffix.'.phtml';
            if (file_exists($layoutFile))
            {
                $this->_layout->setLayout($prefix.$this->_suffix);
                return;
            }
        }
        $this->_layout->setLayout($this->_defaultLayout);
        
//        // layout for backend
//        if ($module == 'backend') 
//        {
//            $layout->setLayout('backend');
//            return;
//        }
//        
//        //testing of access site
//        if (!Zend_Auth::getInstance()->hasIdentity())
//        {
//            if (file_exists($layout->getLayoutPath().'access'.$this->_suffix.'.phtml'))
//            {
//                $layout->setLayout('access'.$this->_suffix);
//            }
//            else
//            {
//                $layout->setLayout('access');
//            }
//        }
//        //check if there if file specific for module and mobile
//        elseif (file_exists($layout->getLayoutPath().$module.$this->_suffix.'.phtml'))
//        {
//            $layout->setLayout($module.$this->_suffix);
//        }
//        //check if there is a file specific for module 
//        elseif (file_exists($layout->getLayoutPath().$module.'.phtml'))
//        {
//            $layout->setLayout($module);
//        }
//        //check if mobile for default layout for mobile
//        elseif (file_exists($layout->getLayoutPath()
//                .$this->_defaultLayout.$this->_suffix.'.phtml')) 
//        {
//            $layout->setLayout($this->_defaultLayout.$this->_suffix);
//        }
    }
    
    protected function _setPrefixDevice()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        $deviceType = $session->deviceType;
        //testing if mobile version
        if ($deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
                $this->_suffix = '.mobile';
        }
    }
    
    
    protected function _setPrefixes($request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        $this->_prefixes = [
            implode('-',[$module,$controller,$action]),
            implode('-',[$module,$controller]),
            $module
        ];
    }
    
    protected function _initJsAndCSS()
    {
        foreach ($this->_prefixes as $prefix)
        {
            $jsFile = '/application/js/'.$prefix.$this->_suffix.'.js';
            if (file_exists(APPLICATION_PATH.'/../public'.$jsFile))
            {
                $this->_layout->getView()->inlineScript()->appendFile($jsFile);
                break;
            }
        }
        
        foreach ($this->_prefixes as $prefix)
        {
            $cssFile = '/application/css/'.$prefix.$this->_suffix.'.css';
            if (file_exists(APPLICATION_PATH.'/../public'.$cssFile))
            {
                $this->_layout->getView()->headLink()->appendStylesheet($cssFile);
                break;
            }
        }
    }
}
