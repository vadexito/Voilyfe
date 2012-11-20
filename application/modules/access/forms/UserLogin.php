<?php

class Access_Form_UserLogin extends Pepit_Form
{

    public function init()
    {
        parent::init();
        $this->setMethod('post');
        
        
        $this->addElements([
            new Access_Form_Elements_UserName('userName'),
            new Access_Form_Elements_UserPassword('userPassword'),
            (new Pepit_Form_Element_Submit('submit_login'))->setLabel('action_login'),
            new Access_Form_Elements_RememberMe('rememberMe'),
        ]);
        
        
        
        
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType === Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            $this->setDecorators([
                ['ViewScript', ['viewScript' => '_loginForm-mobile.phtml']]
            ]);
            
            $this->getElement('submit_login')->setDecorators(['viewHelper'])->setAttrib('data-role','button');
        }
        else
        {
            $this->setAttrib('class','well');
        }
        
        
    }
}

