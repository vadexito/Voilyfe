<?php


trait Pepit_Http_UserAgent_Trait
{
    protected $_siteIsMobile = NULL;
    
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

