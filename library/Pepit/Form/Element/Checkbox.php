<?php

/**
 * define an element text for crete item form
 * 
 *  
 */

class Pepit_Form_Element_Checkbox extends Zend_Form_Element_Checkbox
{
    public function init()
    {
        parent::init();
        
        Pepit_Form_Element::initErrorHelper($this);
        Pepit_Form_Element::initDecoratorPath($this);
        Pepit_Form_Element::initDecorators($this);
        
        $this->addDecorator('label');
    }
}
