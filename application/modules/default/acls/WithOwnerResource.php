<?php

/**
 *
 * user role
 * @package Mylife
 * @author DM 
 */
class Application_Acl_WithOwnerResource implements Zend_Acl_Resource_Interface
{

    public $resourceId ;
    
    public function __construct($resourceId,$ownerId = NULL)
    {
        $this->resourceId = $resourceId;
        $this->ownerId = $ownerId;
    }




    public function getResourceId()
    {
        return $this->resourceId;
    }

}

