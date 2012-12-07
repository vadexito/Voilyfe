<?php

class Members_Model_Settings
{

    use Pepit_Model_Traits_BindForm;
    
    protected $_formClasses = [
        'update' => 'settingsUpdate',
    ];
    
    public function update() 
    {
        foreach ($this->getForm()->getElements() as $element)
        {
            if (method_exists($element,'settingUpdate'))
            {
                $element->settingUpdate();
            }
        }
    }
    
    public function loadForm($formName,$options)
    {
        $class = 'Members_Form_'.ucfirst($formName);
        
        return new $class;
    }
}
    

