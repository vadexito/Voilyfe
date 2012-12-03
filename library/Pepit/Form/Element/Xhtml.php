<?php

/**
 * define an element text for crete item form
 * add div wrapper
 * add error class on div wrapper
 * add * to label if required is activated
 *  
 */

class Pepit_Form_Element_Xhtml extends Zend_Form_Element_Xhtml
{
    use Pepit_Form_Element_Trait_Trait,Pepit_Doctrine_Trait;
            
    public function init()
    {
        parent::init();
        Pepit_Form_Element::initFormElement($this);
    }
    
    /**
     * default mapping function
     * 
     * @param type $formValue
     * @return type
     */
    public function mapElement()
    {
        return $this->getValue();
    }
    
    public function populate($entity)
    {
        $property = $this->getAttrib('data-property-name');
        if ($property && property_exists($entity,$property) && $entity->$property)
        {
            return $entity->$property;
        }
        return false;
    }
}
