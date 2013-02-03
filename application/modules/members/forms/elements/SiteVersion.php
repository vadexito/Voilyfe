<?php
/**
 * form element for item
 *
 * @author DM
 */


class Members_Form_Elements_SiteVersion extends Pepit_Form_Element_Select
{
    protected $_session;
    
    public function init()
    {
        $this->_session = new Zend_Session_Namespace('mylife_device_info');
        $question = Zend_Registry::get('Zend_Locale')->getQuestion();
        $this   ->setMultiOptions([
                    Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE
                        => $question['yes'],
                    Application_Controller_Plugin_MobileInit::DEVICE_TYPE_DESKTOP
                        => $question['no']])
                ->setAttrib('data-role','slider')
                ->setValue($this->_session->deviceType)
                ->setLabel('item_mobile_version');                            
        
        parent::init();
    }
    
    public function settingUpdate()
    {
        $this->_session->deviceChoice = (int)$this->getValue();
    }


}

