<?php

/**
 *
 * Pepit_AssociationType_ManyToMany
 * 
 * @package Mylife
 * @author DM 
 */
class Pepit_AssociationType_ManyToMany extends Pepit_AssociationType
{

    /**
     *
     * @param Doctrine\Entity $entity
     * @param string $key should be in plural form
     * @param array $entityIds array of ids corresponding to elements 
     * in the target entity
     * @param string $targetEntity where the element are stored
     * @return Doctrine\Entity
     * @throws Pepit_Model_Exception in case $entityIds is not an array
     */
    public function hydrate($entity,$key,$entityIds,$targetEntity = NULL) 
    {
        if ($targetEntity === NULL)
        {
            $targetEntity = 'ZC\Entity\ItemRow';
        }
        
        $keySingular = Pepit_Inflector::singularize($key);
        $addMethod = 'add'.ucfirst($keySingular);

        //remove the existing elements
        $entity->$key = new \Doctrine\Common\Collections\ArrayCollection();
        if ($entityIds === NULL)
        {
            return $entity;
        }
        
        if (is_array($entityIds))
        {
            //add entities
            foreach($entityIds as $entityId)
            {
                //add entity to description element
                if ((int)$entityId > 0)
                { 
                    // add new elements
                    $entity->$addMethod($this->em 
                                    ->getRepository($targetEntity)
                                    ->find($entityId)
                    );
                }
            }
        }
        else
        {
            throw new Pepit_AssociationType_Exception('Invalid format (should be array) for many to many association');
        }
        
        return $entity;
    }
}

