<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_OneToMany extends Pepit_AssociationType
{
    /**
     *
     * @param Doctrine\Entity $entity entity to be hydrated
     * @param string $key property name of the entity to be hydrated
     * @param type $value for hydration in form standard (here a string to be parsed into an array
     * @param string $targetEntity entity where to store different values
     * @return Doctrine\Entity hydrated entity 
     */
    
    public function hydrate($entity,$key,$value,$targetEntity = NULL) 
    {
        if ($targetEntity === NULL)
        {
            $targetEntity = 'ZC\Entity\ItemRow';
        }

        //parse value into an array
        $values = explode(
        Pepit_Model_Doctrine2::FORM_ENTRY_SEPARATOR,
            $value
        );
        
        //remove the existing elements
        $entity->$key = new \Doctrine\Common\Collections\ArrayCollection();
        
        
        foreach ($values as $subValue)
        {
            //insert new values
            $itemRow = new $targetEntity;
            $itemRow->value = $subValue;
            $itemRow->creationDate = new \DateTime('now');
            $itemRow->modificationDate = new \DateTime('now');
            $this->em->persist($itemRow);

            //define the relevant add method                    
            $keySingular = Pepit_Inflector::singularize($key);
            $addMethod = 'add'.ucfirst($keySingular);
            
            
            //add to event
            $entity->$addMethod($itemRow);
        }
        
        return $entity;
    }
}

