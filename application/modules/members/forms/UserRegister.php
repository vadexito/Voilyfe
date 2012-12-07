<?php

class Members_Form_UserRegister extends Pepit_Form
{

    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
        
        $userName = new Members_Form_Elements_UserName('userName');
        
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
       
        $email = new Members_Form_Elements_Email('email');
        
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
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('item_first_name')))
                    ->setAttrib('data-property-name','firstName');
        
        
        /* last name */
        $lastName = new Pepit_Form_Element_Text('lastName',array(
            'label' => ucfirst($this->getTranslator()->translate('item_last_name'))
        ));
        $validLength = new Zend_Validate_StringLength(array(
            'min'   => 1,
            'max'   => 64
        ));
        $lastName   ->addFilters(array('StringTrim','StripTags'))
                    ->addValidator($validLength)
                    ->setAttrib('placeholder', ucfirst($this->getTranslator()->translate('item_last_name')))
                    ->setAttrib('data-property-name','lastName');
        
        
        $country =  new Members_Form_Elements_Country('country');
        
        
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
        $submit->setDescription($this->getTosAndPP());
        
        
        
        $this->addElements(array(
            $userName,
            $userPassword,
            $confirmPassword,
            $email,
            $firstName,
            $lastName,
            $country,
        ));
        

        if (!Zend_Registry::get('config')->get('register',false) || 
           (Zend_Registry::get('config')->get('register',false) &&
            Zend_Registry::get('config')->register->form->element->captcha === 'false'))
        {
            $this->addElement($captcha);
        }
        $this->addElement($submit);
        
        
        if (!$this->siteIsMobile())
        {
            $this->setAttrib('class','well');
        }
    }
    
    public function getTosAndPP()
    {
        $linkTerm = '<a href="'
            . $this->getView()->url(array('page' => 'terms'),'static-content'). '">'
            . $this->getTranslator()->translate('Terms of Service'). '</a>';
        $linkPrivatePolicy = '<a href="'
            . $this->getView()->url(array('page' => 'privacy'),'static-content'). '">'
            . $this->getTranslator()->translate('Privacy Policy').'</a>';
        
        $tosAndPP = $this->getTranslator()->translate(
            'By clicking the button, you agree to the %terms1% and to the %terms2%.'
        );
        
        return preg_replace(
            '#(%terms1%)(.*)(%terms2%)#',
            $linkTerm.'$2'.$linkPrivatePolicy,
            $tosAndPP
        );
    }
}

