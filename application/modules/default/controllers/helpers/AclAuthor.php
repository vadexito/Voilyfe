<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Application_Controller_Action_Helper_AclAuthor
    extends Zend_Controller_Action_Helper_Abstract
{

    protected $_acl;
    
    public function __construct()
    {
        $this->_acl = Zend_Registry::get('acl');
    }
    
    public function direct($resource)
    {
        $role = new Application_Acl_MemberRole();
        $action = $this->getRequest()->getActionName();
        
        if (!$this->_acl->isAllowed($role,$resource,$action))
        {
            Zend_Auth::getInstance()->clearIdentity();
            return $this->_redirect(
                $this->view->url(['action' => 'login'],'access')
            );
        }
    }
    
    
}

