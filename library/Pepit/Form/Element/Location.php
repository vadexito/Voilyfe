<?php

/**
 * @author DM
 * @package Mylife
 *  
 */

class Pepit_Form_Element_Location extends Pepit_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formLocation';
    
    public function init()
    {
        $this->setOptions(array(
            'label'     => 'item_location',
            'required'  => 'true',
            'horizontal'=> true,
        ));
        
        parent::init();
    }
    
    public function mapElement()
    {
        $formValue = $this->getValue();
        //location property
        $location = new ZC\Entity\Location();
        
        if (is_array($formValue))
        {
            foreach ($formValue as $property => $value)
            {
                $location->$property = $value;
            }
            Zend_Registry::get('entitymanager')->persist($location);
            return $location;
        }
        else
        {
            return $formValue;
        }
    }
    
    
}
