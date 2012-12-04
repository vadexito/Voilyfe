<?php

/**
 * form for Event update
 * 
 * Author : DM 
 */

class Events_Form_EventUpdate extends Events_Form_EventCreate
{
    public function init()
    {
        parent::init();
        
        //remove not useful element for update
        $this->removeElement('submit_insert');
        
        //create submit element
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setLabel('action_save');
        
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType = 
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            //$submit->setAttrib('class','menu_save ui-btn-right');
        }
        
        $this->addElement($submit);
    }
    
    
    
}

