<?php

class Members_Form_UserUpdate extends Members_Form_UserRegister
{

    public function init()
    {
        parent::init();
        $this->removeElement('submit_register');
        $this->removeElement('userPassword');
        $this->removeElement('confirmPassword');
        $this->getElement('userName')->setAttrib('readOnly','true');
        $this->getElement('userName')->setFilters(array());
        $this->getElement('userName')->setValidators(array());
        $this->setDescription('');
        
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setAttrib('class','btn btn-warning btn-large btn-block');
        $submit->setLabel(ucfirst($this->getTranslator()->translate('action_save')));
        $this->addElement($submit);
    }
}

