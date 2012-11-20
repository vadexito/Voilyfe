<?php

class Application_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $_layout;
    
    protected $_suffix = '';
    
    protected $_defaultLayout = 'layout';
    
    protected $_prefixes = [];
    
    protected $_changesToDefault = NULL;
    
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
    
    
    protected function _changePrefixRule($request)
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
            if($mvcPath['from'] === $mvcDefault);
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
    }
    
    
    protected function _setPrefixes($request)
    {
        $mvc = $this->_changePrefixRule($request);
        $module = $mvc['module'];
        $controller= $mvc['controller'];
        $action= $mvc['action'];
        
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
