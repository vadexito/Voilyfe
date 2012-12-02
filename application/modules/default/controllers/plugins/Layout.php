<?php

class Application_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $_layout;
    
    protected $_suffix = '';
    
    protected $_defaultLayout = 'layout';
    
    protected $_prefixesCss = [];
    protected $_prefixesJs = [];
    protected $_prefixesLayout = [];
    
    protected $_changesToDefault = NULL;
    
    public function dispatchLoopStartup (
        \Zend_Controller_Request_Abstract $request)
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
        
        foreach (array_merge($this->_prefixesLayout,[$this->_defaultLayout]) as $prefix)
        {
            $layoutFile = $layout->getLayoutPath().$prefix.$this->_suffix.'.phtml';
            if (file_exists($layoutFile))
            {
                $this->_layout->setLayout($prefix.$this->_suffix);
                return $prefix.$this->_suffix;
            }
        }
        $this->_layout->setLayout($this->_defaultLayout);
        return $this->_defaultLayout;
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
    
    
    protected function _changePrefixRuleLayout($request)
    {
        if ($this->_changesToDefault === NULL)
        {
            $this->_changesToDefault = Zend_Registry::get('config')->layout
                                                                ->redirect
                                                                ->toArray();
        }
        
        $mvcDefault = [
            'module'        => $request->getModuleName(),
            'controller'    => $request->getControllerName(),
            'action'        => $request->getActionName()
        ];
        
        foreach ($this->_changesToDefault as $mvcPath)
        {
            if ($mvcPath['from'] === $mvcDefault)
            {
                $mvc = [];
                foreach ($mvcDefault as $key => $value)
                {
                    if (array_key_exists($key,$mvcPath['to']))
                    {
                        $mvc[$key] = $mvcPath['to'][$key];
                    }
                    else
                    {
                        $mvc[$key] = $value; 
                    }
                }
                return $mvc;
            }
        }
        return $mvcDefault;
    }
    
    
    protected function _setPrefixes($request)
    {
        $mvc = $this->_changePrefixRuleLayout($request);
        $moduleForLayout = $mvc['module'];
        $controllerForLayout = $mvc['controller'];
        $actionForLayout = $mvc['action'];
        
        $this->_prefixesLayout = [
            implode('-',[$moduleForLayout,$controllerForLayout,$actionForLayout]),
            implode('-',[$moduleForLayout,$controllerForLayout]),
            $moduleForLayout
        ];
        
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        $this->_prefixesJs = [
            implode('-',[$module,$controller,$action]),
            implode('-',[$module,$controller]),
            $module
        ];
        $this->_prefixesCss = $this->_prefixesJs;
        
        
    }
    
    protected function _initJsAndCSS()
    {
        foreach ($this->_prefixesJs as $prefix)
        {
            $jsFile = '/application/js/'.$prefix.$this->_suffix.'.js';
            if (file_exists(APPLICATION_PATH.'/../public'.$jsFile))
            {
                $this->_layout->getView()->inlineScript()->offsetSetFile(600,$jsFile);
                break;
            }
        }
        
        foreach ($this->_prefixesCss as $prefix)
        {
            $cssFile = '/application/css/'.$prefix.$this->_suffix.'.css';
            if (file_exists(APPLICATION_PATH.'/../public'.$cssFile))
            {
                $this->_layout->getView()->headLink()->appendStylesheet($cssFile);
                break;
            }
        }
    }
    
    public function getLayout()
    {
        return $this->_layout;
    }
}
