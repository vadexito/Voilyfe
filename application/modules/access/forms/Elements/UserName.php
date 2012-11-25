<?php
/**
 * form element for item
 *
 * @author DM
 */


class Access_Form_Elements_UserName extends Pepit_Form_Element_Text
{

    public function init()
    {
        $this       ->setLabel(ucfirst($this->getTranslator()->translate('user_name')))
                    ->setRequired('true')
                    ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array('NotEmpty'))
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('user_name')));
        
        parent::init();
        
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType === Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
           $this->setDecorators(['viewHelper','label'])->setAttrib('placeholder',NULL);
        }
        else
        {
            $this->setHorizontal(false);
        }
        
        
        
    }
}

