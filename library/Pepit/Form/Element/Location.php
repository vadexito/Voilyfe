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
    
    public function mapElement($entity)
    {
        $propertyLocation = $this->getAttrib('data-property-name');
         
        $formValue = $this->getValue();
        
        
        
        if (is_array($formValue))
        {
            $location = new ZC\Entity\Location();
            
            foreach ($formValue as $property => $value)
            {
                $location->$property = $value;
            }
            Zend_Registry::get('entitymanager')->persist($location);
            
            $entity->$propertyLocation = $location;
            
            return true;
        }
        else
        {
            $entity->$propertyLocation = $formValue;
            
            return true;
        }
    }
    
    public function populate($entity)
    {
        if (property_exists($entity,'location'))
        {
            return [
                'address' => $entity->location->address,
                'longitude' => $entity->location->longitude,
                'latitude' => $entity->location->latitude,
            ];
        }
        return false;
    }
    
    
}
