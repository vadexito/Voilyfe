<?php

/**
 * form for Item creation
 * 
 * Author : DM 
 */

class Backend_Form_ItemGroupUpdate extends Backend_Form_ItemGroupCreate
{
    public function init()
    {
        parent::init();
        $this->removeElement('submit_insert');
        $this->getElement('name')->setAttrib('readOnly','true');
        $this->getElement('name')->setFilters(array());
        $this->getElement('name')->setValidators(array());
        
        $label = $this->getElement('identifierItemId')->getLabel();
        $newLabel = $label. ' not to be changed';
        $this->getElement('identifierItemId')->setLabel($newLabel);
        $submit = new Pepit_Form_Element_Submit('submit_update',array(
            'label' => 'action_save'
        ));
        $this->addElement($submit);
    }
}

