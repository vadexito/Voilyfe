<?php


class Members_Form_SettingsUpdate extends Pepit_Form
{
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
     
        $locale = new Members_Form_Elements_Language('locale');
        $siteVersion = new Members_Form_Elements_SiteVersion('siteVersion');
        
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setLabel(ucfirst($this->getTranslator()->translate('action_save')));
        
        $this->addElements([$locale]);
        
        $config = Zend_Registry::getInstance()->get('config');
        if (!($config->get('plugin') && $config->get('plugin')->get('mobileInit') 
                && $config->get('plugin')->get('mobileInit')->get('testType')))
        {
            $this->addElement($siteVersion);
        }
        $this->addElements([$submit]);
        
    }
    
    
}

