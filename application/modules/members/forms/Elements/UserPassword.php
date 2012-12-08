<?php

class Members_Form_Elements_UserPassword extends Pepit_Form_Element_Password
{
    public function init()
    {
        $this      ->setRequired('true')
                   ->setAttrib('data-property-name','userPassword')
                   ->setLabel(ucfirst($this->getTranslator()->translate('user_password')));
        
        $validLengthPass = new Zend_Validate_StringLength(array(
            'min'   => 5,
            'max'   => 64
        ));
        $validateEqualPass = new Pepit_Validate_ValidateEqualPass('confirmPassword');
        
        $this       ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array($validLengthPass,$validateEqualPass))
                    ->setAttrib(
                            'placeholder', 
                            ucfirst($this->getTranslator()->translate('user_password'))
                    );
        
        parent::init();
    }
    
    public function mapElement($member)
    {
        $hashFactory = new Pepit_Auth_Hash();
        
        $member->passwordSalt = $hashFactory->getSalt();
        $member->userPassword = $hashFactory->hashPassword(
            $this->getValue(),
            $member->passwordSalt
        );
    }
    
    
    
}

