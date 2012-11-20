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
                $view->inlineScript()->prependFile($pathRel);  
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
                $view->headLink()->prependStylesheet('/jquery_mobile/datebox/datebox.css');
                $view->headScript()->appendFile('/jquery_mobile/datebox/datebox-core.js');
                $view->headScript()->appendFile('/jquery_mobile/datebox/datebox-core-modeCalbox.js');
                $view->headScript()->appendFile($pathRel);  
            }
        }
    }
}

