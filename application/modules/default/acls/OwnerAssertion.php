<?php

/**
 *
 * event assertion to check author is authorized to act over an event
 * 
 * @package Mylife
 * @author DM 
 */
class Application_Acl_OwnerAssertion implements Zend_Acl_Assert_Interface
{

    public function assert(Zend_Acl $acl,Zend_Acl_Role_Interface $role = null,
          Zend_Acl_Resource_Interface $resource = null,$privilege = null)
    {
        if ($role->getRoleId() === Application_Acl_Roles::ADMIN ||
                $role->getRoleId() === Application_Acl_Roles::OWNER)
        {
            return true;
        }
        
        return ($role->memberId == $resource->ownerId);
    }

}

