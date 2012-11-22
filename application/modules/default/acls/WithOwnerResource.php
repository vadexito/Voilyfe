<?php

/**
 *
 * user role
 * @package Mylife
 * @author DM 
 */
class Application_Acl_WithOwnerResource implements Zend_Acl_Resource_Interface
{

    public $ownerId = null;
    public $resourceId ;
    
    public function __construct($resourceId)
    {
        $this->resourceId = $resourceId;
    }




    public function getResourceId()
    {
        return $this->resourceId;
    }

}

