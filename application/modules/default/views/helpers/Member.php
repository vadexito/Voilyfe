<?php

class Application_View_Helper_Member extends Zend_View_Helper_Abstract
{
    protected $_auth;
    
    
    public function __construct()
    {
       $this->_auth = Zend_Auth::getInstance();
    } 
    
    
    public function member()
    {
        if ($this->_auth->hasIdentity())
        {
            return $this->_auth->getIdentity();
        }
        else
        {
            return false;
        }
    }
    
}
