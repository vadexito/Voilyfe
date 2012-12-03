<?php


trait Pepit_Form_Element_Trait_Trait
{

    protected $_horizontal = false;
    
    protected $_storageEntity = NULL;
    
    public function getStorageEntity()
    {
        return $this->_storageEntity;
    }
    
    public function setStorageEntity($entity)
    {
        $this->_storageEntity = $entity;
        return $this;
    }
    
    public function setHorizontal($horizontal)
    {
        $this->_horizontal = $horizontal;
        return $this;
    }
    
    public function getHorizontal()
    {
        return $this->_horizontal;
    }
}

