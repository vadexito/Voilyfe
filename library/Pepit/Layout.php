<?php

class Application_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $_testMobile = true;
    
    protected $_layout;
    
    protected $_isMobile;
    
    protected $_suffix = '';
    
    protected $_defaultLayout = 'layout';
    
    public function dispatchLoopStartup (
        Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $this->_layout = Zend_Layout::getMvcInstance();
        $this->_chooseLayout($module);
        $this->_initCss($module);
        $this->_initJs($module);
    }
    
    protected function _chooseLayout($module)
    {
        $layout = $this->_layout;
        
        // layout for backend
        if ($module == 'backend') 
        {
            $layout->setLayout('backend');
            return;
        }
        
        //testing if mobile version
        if (($layout->getView()->userAgent() &&
            $layout->getView()->userAgent()->getDevice()->getType() === 'mobile')
             || $this->_testMobile)
        {
                $this->_isMobile = true;
                $this->_suffix = '.mobile';
        }
        
        //testing of access site
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            if (file_exists($layout->getLayoutPath().'access'.$this->_suffix.'.phtml'))
            {
                $layout->setLayout('access'.$this->_suffix);
            }
            else
            {
                $layout->setLayout('access');
            }
        }
        //check if there if file specific for module and mobile
        elseif (file_exists($layout->getLayoutPath().$module.$this->_suffix.'.phtml'))
        {
            $layout->setLayout($module.$this->_suffix);
        }
        //check if there is a file specific for module 
        elseif (file_exists($layout->getLayoutPath().$module.'.phtml'))
        {
            $layout->setLayout($module);
        }
        //check if mobile for default layout for mobile
        elseif (file_exists($layout->getLayoutPath()
                .$this->_defaultLayout.$this->_suffix.'.phtml')) 
        {
            $layout->setLayout($this->_defaultLayout.$this->_suffix);
        }
    }
    
    protected function _initCss($module)
    { 
        
        $cssFile = '/application/css/'.$module.$this->_suffix.'.css';
        $cssFileNoSuffix = '/application/css/'.$module.'.css';
        if (file_exists(APPLICATION_PATH.'/../public'.$cssFile))
        {
            $this->_layout->getView()->headLink()->appendStylesheet($cssFile);
        }
        else if (file_exists(APPLICATION_PATH.'/../public'.$cssFileNoSuffix))
        {
            $this->_layout->getView()->headLink()->appendStylesheet($cssFileNoSuffix);
        }
            
    }
    
    protected function _initJs($module)
    {
        
        //init specific mobile files
        $jsFile = '/application/js/'.$module.$this->_suffix.'.js';
        $jsFileNoSuffix = '/application/js/'.$module.'.js';
        
        if (file_exists(APPLICATION_PATH.'/../public'.$jsFile))
        {
            $this->_layout->getView()->inlineScript()->appendFile($jsFile);
        }
        else if (file_exists(APPLICATION_PATH.'/../public'.$jsFileNoSuffix))
        {
            $this->_layout->getView()->inlineScript()->appendFile($jsFile);
        }
    }
}
