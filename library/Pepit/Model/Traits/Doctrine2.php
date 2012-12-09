<?php
/**
 * Model Trait
 *
 * @package    Mylife
 * @author     DM
 */
Trait Pepit_Model_Traits_Doctrine2
{
    /**
     * load entity
     *
     * @param string $name Name of the entity
     * @return Entity
     */
    static public function loadStorage($name)
    {
        // return repository of the entity
        try 
        {
            $storage = Zend_Registry::get('entitymanager')->getRepository($name);
            
        }
        catch (Exception $e)
        {
           return false;
        }
        return $storage;
    }
    
    public function setEntityManager($em = NULL)
    {
        if ($em === NULL)
        {
            $this->$em = Zend_Registry::get('entitymanager');
        }
        $this->_em = $em;
    }
    
    static public function getPropertyName($containerName,$itemName)
    {
        return $containerName.'_'.$itemName;
    }
    
}
