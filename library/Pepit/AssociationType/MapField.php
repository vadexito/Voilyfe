<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_Mapfield extends Pepit_AssociationType
{

    
    public function hydrate($entity,$key,$value,$targetEntity = NULL) 
    {
        if (property_exists($entity,$key))
        {
            $entity->$key = $value;
            return $entity;
        }
        else
        {
            throw new Pepit_Model_Exception('No property defined for this class');
        }
    }

}

