<?php

class Layout_View_Helper_SettingsMobile extends Zend_View_Helper_Abstract
{
    
    public function settingsMobile()
    {
        $settingsMenu = new Zend_Navigation();
        $this->view->navigation()->menu()->setPartial('partial/_settingsMenu.phtml');
        
        if ($this->view->navigation()->findById('user'))
        {
            $page = $this->view->navigation()->findById('user');
            $settingsMenu->addPage($page);
        }
            
        
        return $this->view->navigation()->menu($settingsMenu);
   }
}
