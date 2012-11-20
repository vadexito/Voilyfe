<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_OneToOne extends Pepit_AssociationType
{

    /**
     * create an new item row for the onetoone value
     * @param doctrine\entity $entity
     * @param string $key
     * @param stirng $value
     * @param string $targetEntity
     * @return type 
     */
    public function hydrate($entity,$key,$value,$targetEntity = NULL) 
    {
        if ($targetEntity === NULL)
        {
            $targetEntity = 'ZC\Entity\ItemRow';
        }
        
        $itemRow = new $targetEntity;
        $itemRow->creationDate = new \DateTime('now');
        $itemRow->modificationDate = new \DateTime('now');
        $itemRow->value = $value;
        $this->em->persist($itemRow);
        
        $entity->$key = $itemRow;
        return $entity;
    }
}

