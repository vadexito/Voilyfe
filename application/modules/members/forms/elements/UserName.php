<?php

class Members_Form_Elements_UserName extends Pepit_Form_Element_Text
{
    public function init()
    {
        $validLengthName = new Zend_Validate_StringLength(array(
            'min'   => 4,
            'max'   => 64
        ));
        
        $userUniqueName = new Pepit_Validate_UserUniqueName($this->getEntityManager());
        
        $this       ->setRequired('true')
                    ->addFilters(array('StringToLower','StringTrim',
                        'StripTags','Alnum'))
                    ->addValidators(array($validLengthName,$userUniqueName))
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('user_name')))
                    ->setAttrib('data-property-name','userName')
                    ->setLabel(ucfirst($this->getTranslator()->translate('user_name')));
        
        
        parent::init();
    }
}

