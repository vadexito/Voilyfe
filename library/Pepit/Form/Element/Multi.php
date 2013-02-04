<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


abstract class Pepit_Form_Element_Multi extends Zend_Form_Element_Multi
    implements Pepit_Form_Element_Interface_Interface
{
    use Pepit_Form_Element_Trait_Trait,Pepit_Doctrine_Trait,Pepit_Form_Element_Trait_KeywordsVisual;
    
    
    /**
     * id of the corresponding item in database if item
     * @var integer
     */
    protected $_idDB;
    
    protected $_multioptionTarget = NULL;
    
    public function init()
    {
        $this->setAttrib('data-visualrep','tags');
        
        if ($this->getStorageEntity())
        {
            $repository = $this->getEntityManager()
                        ->getRepository($this->getStorageEntity());
            if ($this->getIdDB())
            {
                $itemRows = $repository->findByItem($this->getIdDB());
            }
            else
            {
                $itemRows = $repository->findAll();
            }
            
            //add multioptions
            
            $this->addMultiOption(null,$this->getTranslator()->translate('msg_pickup_a_value'));
            $this->registerInArrayValidator(false);
            $this->setRequired(false);
            foreach($itemRows as $value)
            {
                $property = $this->getStorageEntityProperty();
                if (property_exists($value,$property))
                {
                    $this->addMultiOption(
                        $value->id,
                        $value->$property
                    );
                }
                else
                {
                    throw new Pepit_Form_Exception('The property '.$property.' is not found in the element '. gettype($value));
                }
                
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
        $property = $this->getAttrib('data-property-name');
        if ($this->getStorageEntity())
        {
            $entity->$property = $this->getEntityManager()
                        ->getRepository($this->getStorageEntity())
                        ->findOneById($formValue);
            return true;
        }
        else
        {
            $entity->$property = $formValue;
            return true;
        }
    }
    
    public function populate($entity)
    {
        $property = $this->getAttrib('data-property-name');
        
        if ($property && property_exists($entity,$property))
        {
            $value = $entity->$property;
            if (is_object($value))
            {
                //array collection
                if (method_exists($value,'count'))
                {
                    $ids = [];
                    foreach ($value as $subvalue)
                    {
                        $ids[] = $subvalue->id;
                    }
                    return $ids;
                }
                //entity
                else
                {
                    if (property_exists($entity->$property,'id'))
                    {
                        return $entity->$property->id;
                    }
                    else
                    {
                        throw new Pepit_Form_Exception('Id property should be defined for : '.$property);
                    }
                }
            }
            
            return $entity->$property;
            
        }
        return false;
    }
}
