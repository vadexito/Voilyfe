<?php

class Members_SettingsController extends Zend_Controller_Action
{

    protected $_member;
    
    public function init()
    {
        $this->_member = Zend_Auth::getInstance()->getIdentity();
    }

    public function indexAction()
    {
        
    }  
       
    public function setpreferencesAction()
    {
        
    }  
       
}