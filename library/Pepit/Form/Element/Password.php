<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


class Pepit_Form_Element_Password extends Zend_Form_Element_Password
{
    use Pepit_Form_Element_Trait_Trait,Pepit_Doctrine_Trait;
    
    public function init()
    {
        Pepit_Form_Element::initFormElement($this);
    }
}
