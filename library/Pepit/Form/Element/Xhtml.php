<?php



class Pepit_Form_Element_Xhtml extends Zend_Form_Element_Xhtml implements Pepit_Form_Element_Interface_Interface
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
    public function mapElement($entity)
    {
        $property = $this->getAttrib('data-property-name');
        $entity->$property = $this->getValue();
        
        return true;
    }
    
    public function populate($entity)
    {
        $property = $this->getAttrib('data-property-name');
        if (!$property)
        {
            throw new Pepit_Form_Exception('A data-property-name attribute must be defined for the element :'.$this->getId());
        }
        if ($property && property_exists($entity,$property) && $entity->$property)
        {
            return $entity->$property;
        }
        return false;
    }
}
