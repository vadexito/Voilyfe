<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_ManyToOne extends Pepit_AssociationType
{

    /**
     *
     * @param Doctrine\Entity $entity
     * @param string $key
     * @param integer $value should be an id of an element in target entity
     * @param string $targetEntity
     * @throws Pepit_Model_Exception
     * @return Doctrine\Entity 
     */
    public function hydrate($entity,$key,$value,$targetEntity = NULL) 
    {
        if ($targetEntity === NULL)
        {
            $targetEntity = 'ZC\Entity\ItemRow';
        }
        
        
        if ((int)$value > 0)
        {
            $entity->$key = $this   ->em
                                    ->getRepository($targetEntity)
                                    ->find($value);
        }
        else if ($value === NULL)
        {
            $entity->$key = NULL;
            return $entity;
        }
        else
        {
            throw new Pepit_AssociationType_Exception(
                'Invalid value for ManyToOne Association (not positive integer)'
            );
        }
        
        return $entity;
        
    }

}

