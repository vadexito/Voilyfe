<?php


trait Pepit_Form_Element_Trait_Trait
{

    use Pepit_Http_UserAgent_Trait;
    
    protected $_horizontal = false;
    protected $_storageEntity = NULL;
    protected $_storageEntityProperty = 'value';
    
    public function getStorageEntity()
    {
        return $this->_storageEntity;
    }
    
    public function setStorageEntity($entity)
    {
        $this->_storageEntity = $entity;
        return $this;
    }
    
    public function getStorageEntityProperty()
    {
        return $this->_storageEntityProperty;
    }
    
    public function setStorageEntityProperty($entity)
    {
        $this->_storageEntityProperty = $entity;
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

