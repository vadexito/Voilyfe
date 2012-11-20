<?php

/**
 * form for erasing element
 * 
 * Author : DM 
 */

abstract class Pepit_Form_Delete extends Pepit_Form
{
    public function init()
    {
        parent::init();
        
        // set attribute for form
        $this->setMethod('post');
        
        // set element for submit button
        $submitDelete = new Pepit_Form_Element_Submit('submit_delete', array(
            'label' => 'action_delete'
        ));
        
        $this->addElements(array(
            $submitDelete
        ));
    }
}

