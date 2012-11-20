<?php

class Backend_Model_Items extends Pepit_Model_Doctrine2 implements Backend_Model_Interface_Container
{

    /**
     * model for items (components of categories)
     * @var string
     */
    protected $_storageName = 'ZC\Entity\Item';
    
    /**
     * form classes for CRUD operations
     * @var array
     */
    
    protected $_formClasses = array(
        'insert' => 'itemCreate',
        'update' => 'itemUpdate',
        'delete' => 'itemDelete'
    );
    
    static public function getFormElementPath($name = null)
    {
        $directory = APPLICATION_PATH.'/modules/Events/forms/Elements/';
        if ($name === NULL)
        {
            return $directory;
        }
        return $directory.ucfirst($name).'.php';
    }
    
    public function insert()
    {
        $newItemId = parent::insert($this->getForm()->getValues());
        
        $this->_save($newItemId);
    }
    
     public function update(array $values, $entityId)
    {
        parent::update($values, $entityId);
        
        $this->_save($entityId);
    }
    
    
    
    public function getFormElementFromDoctrineArray(array $item)
    {
        //define class
        $classFormElement = $item['formElementClass']['name'];
        
        // define for element
        $formElement = new $classFormElement(array(
                            'name'        => $item['name'],
                            'label'       => $item['formLabel'],
                            'required'    => $item['formRequired']
        ));
        
        //add other elements if necessary
        if (isset($item['formValidators']))
        {
            $formElement->setValidators(
                    Pepit_Model_Doctrine2::ResultToArrayByKey(
                                   $item['formValidators'],
                                   'name'
            ));
        }
        
        if (isset($item['formFilters']))
        {
            $formElement->setFilters(
                    Pepit_Model_Doctrine2::ResultToArrayByKey(
                                   $item['formFilters'],
                                   'name'
            ));
        }
        
        if (isset($item['formMultioptions']))
        {
            $formElement->setMultioptions(
                    Pepit_Model_Doctrine2::ResultToArrayByKey(
                                   $item['formMultioptions'],
                                   'name'
            ));
        }
        
        //return form element
        return $formElement;
       
    }
    
    
    
    public function createEntityFromForm() 
    {
        $formValues = $this->getForm()->getValues();
        
        
        // create new category
        $item = new \ZC\Entity\Item;
        $item->name = $formValues['name'];
        $item->addItem($item);
        
        //hydrate entity
        return $this->_saveEntityFromForm($formValues, $item);
    }
    
    public function updateEntityFromForm(array $formValues,$itemId)
    {
        // create new category
        $item = $this->_repository->find($itemId);
        
        //hydrate entity
        return $this->_saveEntityFromForm($formValues, $item);
        
    }
    
    public function delete($itemId)
    {
        $item = $this->getStorage()->find($itemId);
        if ($item)
        {
            //delete line in item table
            parent::delete($itemId);
            
            // delete form element file
            $this->deleteFormElement($item->name);
        }
        else
        {
            throw new Pepit_Model_Exception(
                    'No category to delete in the database'
            );
        }
    }
    
    
    protected function _saveEntityFromForm($item)
    {
        $formValues = $this->getForm()->getValues();
        
        $item->formElementClass = 
            $this->_em
                    ->getRepository('ZC\Entity\FormElementClass')
                    ->find($formValues['formElementClassId']);
        $item->formLabel = $formValues['formLabel'];
        $item->formRequired = $formValues['formRequired'] === '1';
        $item->nullableSQL = $formValues['nullableSQL'] === '1';
        $item->typeSQL = $formValues['typeSQL'];
        $item->sizeSQL = (int)$formValues['sizeSQL'];
        $item->associationType = $formValues['associationType'];
        $item->itemType = $formValues['itemType'];
        
        //add validators
        $item->formValidators = 
                        new \Doctrine\Common\Collections\ArrayCollection();
        $item = $this->hydrateArrayCollectionFromEntity(
                $item,
                'formValidators',
                $formValues['formValidatorIds'],
                'ZC\Entity\FormValidator'
        );
        
        //add filters
        $item->formFilters = 
                        new \Doctrine\Common\Collections\ArrayCollection();
        $item = $this->hydrateArrayCollectionFromEntity(
                $item,
                'formFilters',
                $formValues['formFilterIds'],
                'ZC\Entity\FormFilter'
        );
        
        //add multioptions
        $item->itemRows = 
                        new \Doctrine\Common\Collections\ArrayCollection();
        
        if ($formValues['formMultioptions'])
        {
            $values = explode (',',$formValues['formMultioptions']);
            foreach ($values as $value)
            {
                $classStorageSigleItem = $this->getStorageSingleItemValueName();
                $itemRow = new $classStorageSigleItem;
                $itemRow->value = $value;
                $itemRow->creationDate = new \DateTime;
                $itemRow->modificationDate = new \DateTime;
                $itemRow->item = $item;
                $itemRow->member = $this->getEntityManager()
                                        ->getRepository('ZC\Entity\Member')
                                        ->find($this->_userId);
                $item->addItemRow($itemRow);
            }
        }
        
        return $item;
    }
    
    static public function getStorageSingleItemValueName()
    {
        return 'ZC\Entity\ItemRow';
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
        return $itemName;
//        switch ($itemAssociationType)
//        {
//            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE :
//                $formKey = $itemName;
//                break;
//            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY :
//                $formKey=  $itemName ;
//                break;
//            //one to one and many to one correspond to form_element_text
//            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE :
//                $formKey = $itemName.'Id' ;
//                break;
//            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY :
//                $formKey =  Pepit_Inflector::singularize($itemName).'Ids' ;
//                break;
//            //one to one and many to one correspond to form_element_text
//            default :
//                $formKey = $itemName;
//                break;
//        }
//        return $formKey;
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
    
    public function getArrayForFormUpdateFromEntity($itemId)
    {
        $arrayResult = parent::getArrayForFormUpdateFromEntity($itemId);
        $item = $this->getStorage()->find($itemId);
        
        $formOptions = array();
        foreach($item->formMultioptions as $option)
        {
            $formOptions[] = $option->value;
        }
        $arrayResult['formMultioptions'] = implode(
            ',',
            $formOptions    
        );
        
        return $arrayResult;
                
    }
    
    /**
     * gives the name of the form element class associated with an item
     * 
     * @param string $itemName
     * @return string
     */
    static public function getFormElementClassName($itemName)
    {
        return 'Events_Form_Elements_'.ucfirst($itemName);
    }
    
    public function createNewFormElement(ZC\Entity\Item $item)
    {
        $name = $item->name;
        // define catainer for new class to generate
        $newClass = new Zend_CodeGenerator_Php_Class();
        
        // define class
        $nameClass = self::getFormElementClassName($name);
        $newClass->setName($nameClass.' extends '.$item->formElementClass->name);
        
        //define general docblock
        $docBlock = new Zend_CodeGenerator_Php_Docblock(array(
            'shortDescription' => 'form element for item',
            'tags'             => array(
                array(
                    'name'        => 'author',
                    'description' => 'DM',
                ),
            ),
        ));
        $required = ($item->formRequired) ? "true" : "false";
        $target = $this->getStorageSingleItemValueName();
        $bodyInit = '$this->setOptions(array('."\n"
                    .'"required" => '.$required.','."\n";
        if ($this->_hasFormMultioption($item))
        {
            $bodyInit.= '"idDB" => '.$item->id.','."\n"
                        .'"multioptionTarget" => \''.$target.'\','."\n";
        }
        
        
        
        if ($item->formFilters)
        {
            $filters = '';
            foreach($item->formFilters as $filter)
            {
                $filters = implode(',',array(
                    "'$filter->name'",
                    $filters
                ));
            }
            $bodyInit.= '"filters" => array('.$filters.'),'."\n";
        }
        
        
        if ($item->formValidators)
        {
            $validators = '';
            foreach($item->formValidators as $validator)
            {
                $validators = implode(',',array(
                    "'$validator->name'",
                    $validators
                ));
            }
            $bodyInit = $bodyInit.'"validators" => array('.$validators.'),'."\n";
        }
        
        if ($item->itemRows && !$item->associationType)
        {
            $multioptions = '';
            foreach($item->itemRows as $multioption)
            {
                $multioptions = implode(',',array(
                    "'$multioption->value'",
                    $multioptions
                ));
            }
            $bodyInit = $bodyInit.'"multioptions" => array('.$multioptions.'),'."\n";
        }
        $bodyInit.= '));'."\n"
                         ."parent::init();\n";
        
        
        $method = new Zend_CodeGenerator_Php_Method(array(
            'name' => 'init',
            'body' => $bodyInit
        ));
        
        $newClass->setMethod($method);
        // prepare file for class
        $file = new Zend_CodeGenerator_Php_File(array(
            'classes'            => array($newClass),
            'docBlock'           => $docBlock
        ));
        
        // generate code
        $code = $file->generate();
        
        // add code to file
        file_put_contents(
            $this->getFormElementPath($item->name),
            $code
        );
    }
    
    protected function _hasFormMultioption($item)
    {
        $formElementClasses = array(
            'Pepit_Form_Element_Select',
            'Pepit_Form_Element_MultiCheckbox',
        );
        return in_array($item->formElementClass->name,$formElementClasses);
    }


    public function deleteFormElement($itemName)
    {
        //delete existing file
        $path = $this->getFormElementPath($itemName);
        if (file_exists($path))
        {
            unlink($path);
        }
        
    }
    
    static public function getRowContainerEntityName($name)
    {
        return 'ZC\Entity\ItemRow';
    }
    static public function getNameSpaceForContainer()
    {
        return 'ZC\Entity';
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
        return $initialSlash.'ZC\Entity\Item';
    }
    static public function getContainerForRowsPath($name)
    {
        return APPLICATION_PATH.'/library/ZC/Entity/ItemRow.php';
    }
    
    static public function getRowContainerShortEntityName($entityName)
    {
        return ;
    }
    
    static public function getContainerRowModel()
    {
        return 'Events_Model_ItemRows';
    }
    
    protected function _save($entityId)
    {
        $entity = $this->getStorage()->find($entityId);
        
        $this->createNewFormElement($entity);
       
    }
}

