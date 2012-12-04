<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


abstract class Pepit_Form_Element_Multi extends Zend_Form_Element_Multi
    implements Pepit_Form_Element_Interface_Interface
{
    use Pepit_Form_Element_Trait_Trait,Pepit_Doctrine_Trait;
    
    
    /**
     * id of the corresponding item in database
     * @var integer
     */
    protected $_idDB;
    
    protected $_multioptionTarget = NULL;
    
    public function init()
    {
        if ($this->getStorageEntity())
        {
            $itemRows = $this->getEntityManager()
                        ->getRepository($this->getStorageEntity())
                        ->findByItem($this->getIdDB());
                        
            //add multioptions 
            foreach($itemRows as $value)
            {
                $this->addMultiOption(
                        $value->id,
                        $value->value
                );
            }
        }
        
        Pepit_Form_Element::initFormElement($this);
        
    }
    
    /**
     * Retrieve target for multioptions
     *
     * @return mixed
     */
    public function getMultioptionTarget()
    {
        return $this->_multioptionTarget;
    }

    /**
     * Set target for multioptions
     *
     * @param mixed $target
     * @return self
     */
    public function setMultioptionTarget($target)
    {
        $this->_multioptionTarget = $target;
        return $this;
    }
    
    public function getIdDB()
    {
        return $this->_idDB;
    }

    public function setIdDB($id)
    {
        $this->_idDB = $id;
        return $this;
    }
    
    public function mapElement($entity)
    {
        $formValue = $this->getValue();
        if ($this->getStorageEntity())
        {
            return $this->getEntityManager()
                        ->getRepository($this->getStorageEntity())
                        ->findOneById($formValue);
        }
        
        $property = $this->getAttrib('data-property-name');
        $entity->$property = $formValue;
        
        return true;
    }
    
    public function populate($entity)
    {
        $property = $this->getAttrib('data-property-name');
        if ($property && property_exists($entity,$property))
        {
            return $entity->$property->id;
        }
        return false;
    }
    
    public function dataChart($events)
    {
                
        $tags = [];
        $property = $this->getAttrib('data-property-name');
        $propertyTag = 'value';
        
        foreach ($events as $event)
        {
            if ($event->$property)
            {
                if (!method_exists($event->$property,'count'))
                {
                    $tags[] = $event->$property->$propertyTag;
                }
                else
                {
                    foreach ($event->$property as $tag)
                    {
                        $tags[] = $tag->$propertyTag;
                    }
                }
            }
            
        }
        $entities = array_count_values($tags);
        arsort($entities);
      
        return [
            'type'  =>'winner_list',
            'title' => ucfirst($this->getLabel()),
            'values'=> $entities
        ];
        
    }
}
