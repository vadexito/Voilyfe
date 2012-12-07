<?php

class Members_Form_Elements_Email extends Pepit_Form_Element_Text
{
    public function init()
    {
        $this      ->setRequired('true')
                    ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array('EmailAddress'))
                    ->setAttrib(
                            'placeholder', 
                            ucfirst($this->getTranslator()->translate('item_email')))
                    ->setAttrib('data-property-name','email')
                    ->setLabel(ucfirst($this->getTranslator()->translate('item_email')));
                    
       parent::init();
    }
}

