<?php

/**
 *
 * user role
 * @package Mylife
 * @author DM 
 */
class Application_Acl_UserResource implements Zend_Acl_Resource_Interface
{

    public $ownerId = null;
    public $resourceId = 'members:user';
    
    public function getResourceId()
    {
        return $this->resourceId;
    }

}

