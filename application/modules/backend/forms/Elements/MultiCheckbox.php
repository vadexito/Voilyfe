<?php

class Backend_Form_Elements_MultiCheckbox extends Pepit_Form_Element_MultiCheckbox
{
    public function init()
    {
        parent::init();
        $this->addDecorator('label')->setSeparator('')
             ->setAttrib('label_class','checkbox inline');        
        
    }
    
    
}

