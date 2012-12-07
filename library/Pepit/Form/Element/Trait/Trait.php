<?php


trait Pepit_Form_Element_Trait_Trait
{

    protected $_horizontal = false;
    protected $_storageEntity = NULL;
    protected $_siteIsMobile = NULL;
    
    public function getStorageEntity()
    {
        return $this->_storageEntity;
    }
    
    public function setStorageEntity($entity)
    {
        $this->_storageEntity = $entity;
        return $this;
    }
    
    public function setHorizontal($horizontal)
    {
        $this->_horizontal = $horizontal;
        return $this;
    }
    
    public function getHorizontal()
    {
        return $this->_horizontal;
    }
    
    public function siteIsMobile()
    {
        if ($this->_siteIsMobile === NULL)
        {
            $session = new Zend_Session_Namespace('mylife_device_info');
            $this->_siteIsMobile = ($session->deviceType ===
                    Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE);
        }
        return $this->_siteIsMobile;
        
    }
}

