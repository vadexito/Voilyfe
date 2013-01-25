<?php

/**
 * form to delete category
 * 
 * Author : DM 
 */

class Backend_Form_UpdateDoctrineDB extends Pepit_Form
{
    public function init()
    {
        parent::init();
        
        // set attribute for form
        $this->setMethod('post');
        
        // set element for submit button
        $submit = new Pepit_Form_Element_Submit('submit_update', array(
            'label' => 'Update data base'
        ));
        
        $this->addElements([
            $submit
        ]);
    }
}

