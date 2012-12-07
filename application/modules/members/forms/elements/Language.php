<?php
/**
 * form element for item
 *
 * @author DM
 */


class Members_Form_Elements_Language extends Pepit_Form_Element_Select
{
    public function init()
    {
        $config = new Zend_Config_Xml(
            APPLICATION_PATH.'/modules/backend/Data/Init/InitialData.xml',
            'init'
        );
        
        $localeValues = [];
        foreach ($config->language->type as $locale)
        {
            $localeValues[$locale->value] = 
                    Zend_Locale::getTranslation(
                            $locale->value,
                            'language',
                            $locale->value
            );
        }
        
        $this->setMultiOptions($localeValues)->setLabel(('menu_language'));                                

        parent::init();
    }
    
    public function settingUpdate()
    {
        $session = new Zend_Session_Namespace('Mylife_locale');
        $session->locale = $this->getValue();

    }


}

