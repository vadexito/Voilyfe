<?php

/**
 * form for category update
 * 
 * Author : DM 
 */

class Backend_Form_CategoryUpdate extends Backend_Form_CategoryCreate
{
    public function init()
    {
        parent::init();
        
        $this->removeElement('submit_insert');
        
        $this->getElement('name')->setAttrib('readOnly','true')
                                 ->setFilters([])
                                 ->setValidators([]);
        
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setLabel('action_save');
        
        $this->addElement($submit);
    }
        
}

