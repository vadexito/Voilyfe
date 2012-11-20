<?php

trait Pepit_Doctrine_Trait 
{
    protected $_em;
    
    public function getEntityManager()
    {
        if ($this->_em === NULL)
        {
            return Zend_Registry::get('entitymanager');
        }
        return $this->_em;
    }

}

