<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


class Pepit_Form_Element_Select extends Pepit_Form_Element_Multi 
    
{
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelect';
    
    
    
    public function init()
    {
        parent::init();
    }
    
}
