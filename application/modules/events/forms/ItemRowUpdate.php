<?php

/**
 * form for Event update
 * 
 * Author : DM 
 */

class Events_Form_ItemRowUpdate extends Events_Form_ItemRowCreate
{
    public function init()
    {
        parent::init();
        
        //remove not useful element for update
        $this->removeElement('submit_insert');
        
        //create submit element
        $submit = new Pepit_Form_Element_Submit('submit_update');
        $submit->setLabel('action_save');
        $this->addElement($submit);
    }
}

