<?php

class Members_Form_UserRegister extends Pepit_Form
{

    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
        
        /* userName */
        $userName = new Pepit_Form_Element_Text('userName',array(
            'label' => ucfirst($this->getTranslator()->translate('user_name'))
        ));
        $validLengthName = new Zend_Validate_StringLength(array(
            'min'   => 4,
            'max'   => 64
        ));
        $userUniqueName = new Pepit_Validate_UserUniqueName($this->getEntityManager());
        $userName   ->setRequired('true')
                    ->addFilters(array('StringToLower','StringTrim',
                        'StripTags','Alnum'))
                    ->addValidators(array($validLengthName,$userUniqueName))
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('user_name')));
        
        
        /* password */
        $userPassword = new Pepit_Form_Element_Password('userPassword',array(
            'label' => ucfirst($this->getTranslator()->translate('user_password'))
        ));
        $validLengthPass = new Zend_Validate_StringLength(array(
            'min'   => 5,
            'max'   => 64
        ));
        $validateEqualPass = new Pepit_Validate_ValidateEqualPass('confirmPassword');
        $userPassword->setRequired('true')
                    ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array($validLengthPass,$validateEqualPass))
                    ->setAttrib(
                            'placeholder', 
                            ucfirst($this->getTranslator()->translate('user_password'))
                    );
        
         
       
        /* password check */
        $confirmPassword = new Pepit_Form_Element_Password('confirmPassword',array(
            'label' => ucfirst($this->getTranslator()->translate('item_confirmPassword'))
        ));
        $validLengthConf = new Zend_Validate_StringLength(array(
            'min'   => 5,
            'max'   => 64
        ));
        $confirmPassword
                    ->setRequired('true')
                    ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array($validLengthConf))
                    ->setAttrib(
                            'placeholder',
                            ucfirst($this->getTranslator()->translate('item_confirmPassword'))
                    );
       
        
        
        /* email address */
        $email = new Pepit_Form_Element_Text('email',array(
            'label' => ucfirst($this->getTranslator()->translate('item_email'))
        ));
        $email      ->setRequired('true')
                    ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array('EmailAddress'))
                    ->setAttrib(
                            'placeholder', 
                            ucfirst($this->getTranslator()->translate('item_email'))
                    );
       
        
        /* first name */
        $firstName = new Pepit_Form_Element_Text('firstName',array(
            'label' => ucfirst($this->getTranslator()->translate('item_first_name'))
        ));
        $validLength = new Zend_Validate_StringLength(array(
            'min'   => 1,
            'max'   => 64
        ));
        $firstName  ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array($validLength))
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('item_first_name')));
        
        
        /* last name */
        $lastName = new Pepit_Form_Element_Text('lastName',array(
            'label' => ucfirst($this->getTranslator()->translate('item_last_name'))
        ));
        $validLength = new Zend_Validate_StringLength(array(
            'min'   => 1,
            'max'   => 64
        ));
        $lastName   ->addFilters(array('StringTrim','StripTags'))
                    ->addValidators(array($validLength))
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('item_last_name')));
        
        
        /* country */
        $country = new Pepit_Form_Element_Select('countryId');
        
        if ($this->getEntityManager()->getRepository('ZC\Entity\ItemMulti\Country'))
        {
            $options = $this->getEntityManager()->getRepository('ZC\Entity\ItemMulti\Country')->findAll();
            
            $listCountries = Zend_Locale_Data::getList(Zend_Locale::findLocale(),'territory');
            
            foreach ($options as $option)
            {
                $country->addMultioption(
                    $option->id,
                    $listCountries[$option->value]
                );
            }
        }
        
        /* captcha */
        $captcha = new Pepit_Form_Element_Captcha('captcha',array(
            'captcha' => array(
                'captcha'   => 'Figlet',
                'wordLen'   => 5,
                'timeout'   => 300,
                ),
        ));
        $captcha->setAttrib(
                'placeholder',
                ucfirst($this->getTranslator()->translate('item_captcha'))
        );
        
        
        /* submit button */
        $submit = new Pepit_Form_Element_Submit('submit_register');
        $submit->setAttrib('class','btn btn-warning btn-large btn-block');
        $submit->setLabel('action_register');
        
        $this->addElements(array(
            $userName,
            $userPassword,
            $confirmPassword,
            $email,
            $firstName,
            $lastName,
            $country,
        ));
        

        if (Zend_Registry::get('config')->get('register') &&
            Zend_Registry::get('config')->register->form->element->captcha === 'false')
        {
            $this->addElement($captcha);
        }
        $this->addElement($submit);
        
        
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType === Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
           
        }
        else
        {
            $this->setAttrib('class','well');
        }
    }
}

