<?php

/**
 * form for item updating
 * 
 * Author : DM 
 */

class Backend_Form_ItemUpdate extends Backend_Form_ItemCreate
{
    public function init()
    {
        parent::init();
        $this->removeElement('submit_insert');
        $this->getElement('name')->setAttrib('readOnly','true');
        $this->getElement('name')->setFilters(array());
        $this->getElement('name')->setValidators(array());
        
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setLabel('action_save');
        $this->addElement($submit);
    }
}

