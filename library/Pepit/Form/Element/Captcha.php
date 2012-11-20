<?php

/**
 * define an element text for crete item form
 * add div wrapper
 * add error class on div wrapper
 * add * to label if required is activated
 *  
 */


class Pepit_Form_Element_Captcha extends Zend_Form_Element_Captcha
{
    public function init()
    {
        Pepit_Form_Element::initFormElement($this);
        
        $this->removeDecorator('viewHelper');
    }
}
