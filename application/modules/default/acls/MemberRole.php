<?php

/**
 *
 * user role
 * @package Mylife
 * @author DM 
 */
class Application_Acl_MemberRole implements Zend_Acl_Role_Interface
{

    public $role ;
    public $memberId;
    
    public function __construct()
    {
        $this->role = Application_Acl_Roles::MEMBER;
        
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->memberId = Zend_Auth::getInstance()->getIdentity()->id;
        }  
    }


    public function getRoleId()
    {
        return $this->role;
    }
}

