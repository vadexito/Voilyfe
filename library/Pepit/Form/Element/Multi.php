<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


abstract class Pepit_Form_Element_Multi extends Zend_Form_Element_Multi
    implements Pepit_Form_Element_Interface_Interface
{
    /**
     * id of the corresponding item in database
     * @var integer
     */
    protected $_idDB;
    
    protected $_multioptionTarget = NULL;
    
    protected $_em = null;
    
    protected $_storage = null;
    /**
     *
     * @var boolean true if the rendering should be horizontal
     */
    protected $_horizontal = false;

    public function init()
    {
        if ($this->getStorageEntity())
        {
            $itemRows = Zend_Registry::get('entitymanager')
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
    
    public function setHorizontal($horizontal)
    {
        $this->_horizontal = $horizontal;
        
        return $this;
    }
    
    public function mapElement()
    {
        $formValue = $this->getValue();
        if ($this->getStorageEntity())
        {
            return $this->getEntityManager()
                        ->getRepository($this->getStorageEntity())
                        ->findOneById($formValue);
        }
        return $formValue;
    }
    
    public function getEntityManager()
    {
        if ($this->_em === NULL)
        {
            return Zend_Registry::get('entitymanager');
        }
        return $this->_em;
        
    }
    
    public function getStorageEntity()
    {
        return $this->_storage;
    }
    
    public function setStorageEntity($entity)
    {
        $this->_storage = $entity;
    }
}
