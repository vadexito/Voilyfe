<?php

/**
 * Abstract Class model for Events, ItemGroupRows and ItemRows
 *
 * @author     DM
 */

abstract class Events_Model_Abstract_GeneralizedItemRowsAbstract extends Pepit_Model_Doctrine2
{
    
    protected $_formValues;
    
    
    /**
     *
     * @param array $formValues
     * @param mixed $generalizedItemRow either ZC\Entity\Event
     * or ZC\Entity\ItemGroupRow
     * @return mixed
     * @throws Pepit_Model_Exception 
     */
    public function saveEntityFromForm($formValues,$generalizedItemRow)
    {
        $generalizedItemRow->modificationDate = new \DateTime();
        
        //get entity fields from metadata
        $classMetadata = $this->getEntityManager()
                              ->getMetadataFactory()
                              ->getMetadataFor(get_class($generalizedItemRow));
        $fields = array_keys($classMetadata->fieldMappings);
        $associationMappings = $classMetadata->associationMappings;
        
        
        //hydrate map fields
        $this->_formValues = $formValues;
        $generalizedItemRow = $this->_hydrateMapFields(
                $generalizedItemRow,
                $fields
        );
        
        //hydrate associations
        $generalizedItemRow = $this->_hydrateAssociations(
            $generalizedItemRow,
            $associationMappings
        );
        
        if (is_array($this->_formValues))
        {
            foreach ($this->_formValues as $value)
            {
                if ($value)
                {
                    throw new Pepit_Model_Exception(
                            'Invalid key(s) provided for this entity'
                    );
                }
            }
        }
        return $generalizedItemRow;
    }
    
    protected function _hydrateMapFields(
        $generalizedItemRow,$fields,$formValues = NULL)
    {
        if ($formValues === NULL)
        {
            $formValues = $this->_formValues;
        }
        //check the fields
        foreach ($fields as $field)
        {
            if (key_exists($field,$formValues))
            {
                $associationType = Pepit_AssociationType_Factory::build(
                    Pepit_AssociationType::MAP_FIELD,$this->_em
                );
                $generalizedItemRow = $associationType->hydrate(
                    $generalizedItemRow,
                    $field,
                    $formValues[$field]
                );
                unset($this->_formValues[$field]);
            }
        }
        return $generalizedItemRow;
    }
    
    /**
     *
     * @param mixed Doctrine\Entity $generalizedItemRow either itemRow or itemgroupRow or event
     * @param array $associationMappings
     * @param array $formValues (containing formkeys by definition)
     * @return mixed Doctrine\Entity 
     */
    protected function _hydrateAssociations($generalizedItemRow,
            $associationMappings,$formValues = NULL)
    {
        if ($formValues === NULL)
        {
            $formValues = $this->_formValues;
        }
        foreach ($associationMappings as $propertyName => $metadata)
        {
            $associationType = $metadata['type'];
            
            //get the right association type for the one to many case
            // which has metadata many to many plus join table (the many to many
            // has suffix Ids in contrary to the one to many
            
            if ($associationType === 
                    \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY)
            {
                $testOneToMany = array_key_exists(
                        Backend_Model_Items::getFormItemName(
                            $propertyName,
                            \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
                        ),
                        $formValues
                );
                if ($testOneToMany)
                {
                    $associationType = 
                        \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY;
                }
            }
            
            //get form key
            $formKey = Backend_Model_Items::getFormItemName(
                $propertyName,
                $associationType
            );
            
           
            if (array_key_exists($formKey,$formValues))
            {
                $associationType = 
                    Pepit_AssociationType_Factory::build(
                            $associationType,
                            $this->_em
                    );
                $generalizedItemRow = $associationType->hydrate(
                        $generalizedItemRow,
                        $propertyName,
                        $formValues[$formKey],
                        $metadata['targetEntity']
                );
                unset($this->_formValues[$formKey]);
            }
        }
        
        return $generalizedItemRow;
    }
       
        
        
    public function updateEntityFromForm($rowId)
    {
        $row = $this->_repository->find($rowId);
        
        return $this->saveEntityFromForm($this->getForm()->getValues(), $row);
    }
    
    static public function getPropertyName($containerName,$itemName)
    {
        return $containerName.'_'.$itemName;
    }
    
    
    
}

