<?php

class Backend_Model_ItemGroups extends Backend_Model_Abstract_Container
{

    /**
     * model for item groups
     * @var string
     */
    protected $_storageName = 'ZC\Entity\ItemGroup';
    
    /**
     * form classes for CRUD operations
     * @var array
     */
    
    protected $_formClasses = array(
        'insert' => 'itemGroupCreate',
        'update' => 'itemGroupUpdate',
        'delete' => 'itemGroupDelete'
    );
    
    const CONTAINER_ENTITY_SUFFIX  = "ItemGroupRows";
    
    public function insert()
    {
        $formData = $this->getForm()->getValues();
        
        $newItemGroupId = parent::insert($formData);
        
        $this->_save($newItemGroupId);
    }
    
    public function update(array $values, $entityId)
    {
        parent::update($values, $entityId);
        
        $this->_save($entityId);
    }
    
     protected function _save($entityId)
    {
         $entity = $this->getStorage()->find($entityId);
                  
         $this->createContainerEntity($entity); 
        
        //updatedatabase
        Pepit_Doctrine_Tool::updateDoctrineSchemaForCreatingNewContainer(
                $this->_em,
                $this->getParentClassContainer()
        );
    }
    
    
    public function delete($itemGroupId)
    {
        $itemGroup = $this->getStorage()->find($itemGroupId);
        $name = $itemGroup->name;
        
        parent::delete($itemGroupId);
        
        unlink($this->getContainerForRowsPath($name));
        
        //updatedatabase
        Pepit_Doctrine_Tool::updateDoctrineSchemaForCreatingNewContainer(
                $this->_em,
                $this->getParentClassContainer()
        );    
    }

    
    
    

//    public function getFormElementFromDoctrineArray(array $itemGroup)
//    {
//        //define class
//        $classFormElement = $itemGroup['formElementClass']['name'];
//        
//        // define for element
//        $formElement = new $classFormElement(array(
//                            'name'        => $itemGroup['name'],
//                            'label'       => $itemGroup['formLabel'],
//                            'required'    => $itemGroup['formRequired']
//        ));
//        
//        //add other elements if necessary
//        if (isset($itemGroup['formValidators']))
//        {
//            $formElement->setValidators(
//                    Pepit_Model_Doctrine2::ResultToArrayByKey(
//                                   $itemGroup['formValidators'],
//                                   'name'
//            ));
//        }
//        
//        if (isset($itemGroup['formFilters']))
//        {
//            $formElement->setFilters(
//                    Pepit_Model_Doctrine2::ResultToArrayByKey(
//                                   $itemGroup['formFilters'],
//                                   'name'
//            ));
//        }
//        
//        if (isset($itemGroup['formMultioptions']))
//        {
//            $formElement->setMultioptions(
//                    Pepit_Model_Doctrine2::ResultToArrayByKey(
//                                   $itemGroup['formMultioptions'],
//                                   'name'
//            ));
//        }
//        
//        //return form element
//        return $formElement;
//       
//    }
    
    
    
    public function createEntityFromForm() 
    {
        $formValues = $this->getForm()->getValues();

        // create new category
        $itemGroup = new \ZC\Entity\ItemGroup;
        $itemGroup->name = $formValues['name'];
        $itemGroup->creationDate = new \DateTime();
        $itemGroup->identifierItem = $this  ->_em
                                            ->getRepository('ZC\Entity\Item')
                                            ->find($formValues['identifierItemId']);
        
        //hydrate entity
        return $this->_saveEntityFromForm($formValues,$itemGroup);
    }
    
    public function updateEntityFromForm($itemId)
    {
        $formValues = $this->getForm()->getValues();  
        
        // create new category
        $item = $this->_repository->find($itemId);
        
        //hydrate entity
        return $this->_saveEntityFromForm($formValues, $item);
        
    }
    
    protected function _saveEntityFromForm($itemGroup)
    {
        $formValues = $this->getForm()->getValues();
        
        $itemGroup->modificationDate = new  \DateTime();
        
        //list of items
        $itemGroup->items = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($formValues['itemIds'] as $itemId)
        {
            $item = $this->_em->getRepository('ZC\Entity\GeneralizedItem')->find($itemId);
            $itemGroup->addItem($item);
        }
        
        //type of form element
        $itemGroup->formElementClass = 
            $this->_em
                    ->getRepository('ZC\Entity\FormElementClass')
                    ->find($formValues['formElementClassId']);
        
        
        $itemGroup->formRequired = $formValues['formRequired'] === '1';
        $itemGroup->associationType = $formValues['associationType'];
        $itemGroup->itemType = $formValues['itemType'];
        
        //no possibility no changer identifier item
        $itemGroup->identifierItem = $itemGroup->identifierItem;
        
        return $itemGroup;
    }

        /**
     * provides the name of the item form key corresponding to an item name
     * checks plural or singular of the name also
     * (from Name to NameIds for an many to many for example)
     * 
     * @param string $itemName
     * @param string $itemAssociationType
     * @return string 
     */
    static public function getFormItemName($itemName,$itemAssociationType)
    {
        switch ($itemAssociationType)
        {
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE :
                $formKey = $itemName;
                break;
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY :
                $formKey=  $itemName ;
                break;
            //one to one and many to one correspond to form_element_text
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE :
                $formKey = $itemName.'Id' ;
                break;
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY :
                $formKey =  Pepit_Inflector::singularize($itemName).'Ids' ;
                break;
            //one to one and many to one correspond to form_element_text
            default :
                $formKey = $itemName;
                break;
        }
        return $formKey;
    }
    
    /**
     * return the original item name form key entry and check the form key
     * @param string $formKey
     * @param string $itemAssociationType association type of the parameter
     * @return string
     * @throws Pepit_Model_Exception if the form key has no the format
     * corresponding to the association type
     */
    
    static public function getItemNameFromFormItemKey(
                                            $formKey,$itemAssociationType)
    {
        switch ($itemAssociationType)
        {
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE :
               $check = (Pepit_Inflector::singularize($formKey) === $formKey);
               $itemName = $formKey;
               break;
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY :
               $check = (Pepit_Inflector::pluralize($formKey) === $formKey);
               $itemName = $formKey;
               break;
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE :
               $check = preg_match('#(.*)Id#',$formKey);
               $itemName = preg_replace('#(.*)Id#','$1',$formKey);
               break;
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY :
               $check = preg_match('#(.*)Ids#',$formKey);
               $itemName = Pepit_Inflector::pluralize(
                    preg_replace('#(.*)Ids#','$1',$formKey)
                );
               break;
           default :
               $check = true;
               $itemName = $formKey;
               break;
        }
        if (!$check)
        {
            throw new Pepit_Model_Exception(
                'Name of form key invalid forassociation type considered'
            );
        }
        return $itemName;
    }
    
    public function getArrayForFormFromEntity($item)
    {
        $arrayResult = parent::getArrayForFormFromEntity($item);
        
        $formOptions = array();
        foreach($item->formMultioptions as $option)
        {
            $formOptions[] = $option->value;
        }
        $arrayResult['formMultioptionIds'] = implode(
            ',',
            $formOptions    
        );
        
        return $arrayResult;
                
    }
 
    static public function getNameSpaceForContainer()
    {
        return 'ZC\Entity\\'.self::CONTAINER_ENTITY_SUFFIX;
    }
    
    static public function getContainerForRowsPath($name = NULL)
    {
        $directory = APPLICATION_PATH.'/../library/ZC/Entity/'
                .self::CONTAINER_ENTITY_SUFFIX
                .'/';
        if ($name === NULL)
        {
            return $directory;
        }
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        return $directory.ucfirst($name).$suffix.'.php';
    }
    
    static public function getRowContainerEntityName($name)
    {
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        
        return 'ZC\Entity\\'.self::CONTAINER_ENTITY_SUFFIX.'\\'.ucfirst($name).$suffix;
    }
    
    static public function getParentClassContainer($noInitialSlash = true)
    {
        if ($noInitialSlash === false)
        {
            $initialSlash = '\\';
        }
        else
        {
            $initialSlash = '';
        }
        return $initialSlash.'ZC\Entity\ItemGroupRow';
    }
    
    static public function getRowContainerShortEntityName($entityName)
    {
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        return ucfirst($entityName).$suffix;
    }
    
    static public function getContainerRowModel()
    {
        return 'Events_Model_'.self::CONTAINER_ENTITY_SUFFIX;
    }
    
    
    
}

