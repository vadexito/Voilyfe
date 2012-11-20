<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Events_Controller_Action_Helper_Calendar 
    extends Zend_Controller_Action_Helper_Abstract
{

    public function __construct()
    {
        $this->_config = Zend_Registry::get('config');
    }
    
    public function direct($view)
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        $deviceType = $session->deviceType;
        
        //get locale for localization
        $locale = Zend_Registry::get('Zend_Locale');
        $language = $locale->getLanguage();
            
        if ($deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_DESKTOP)
        {
            $pathRel = sprintf(
                    $this->_config->library->jquery->ui->calendar_lang->relativePath,
                    $language
            );
            
            if (file_exists(APPLICATION_PATH.'/../public'.$pathRel))
            {
                $view->inlineScript()->offsetSetFile(200,$pathRel);  
            }
        }
        else if ($this->getRequest()->getActionName() === 'index')
        {
            $pathRel = sprintf(
                    $this->_config->library->jquery->mobile->calendar_lang->relativePath,
                    $language
            );
            
            if (file_exists(APPLICATION_PATH.'/../public'.$pathRel))
            {
                $view->inlineScript()->offsetSetFile(110,$pathRel);  
            }
        }
    }
}

