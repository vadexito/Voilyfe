<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


class Pepit_Form_Element_Password extends Zend_Form_Element_Password
{
    public function init()
    {
        Pepit_Form_Element::initFormElement($this);
    }
}
