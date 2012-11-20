<?php

class Application_View_Helper_DeviceType extends Zend_View_Helper_Abstract
{
    public function deviceType()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        if (isset($session->deviceType))
        {
            return $session->deviceType;
        }
    }
}
