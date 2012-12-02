<?php

/**
 * Class model for categories
 *
 * @author     DM
 */

class Backend_Model_Categories extends Backend_Model_Abstract_Container
{
    
    protected $_formClasses = array(
        'insert' => 'categoryCreate',
        'update' => 'categoryUpdate',
        'delete' => 'categoryDelete'
    );
    
     /**
     * entity name
     * @var array
     */
    protected $_storageName = 'ZC\Entity\Category';
    
    protected $_modelEvents;
    
    const CONTAINER_ENTITY_SUFFIX = "Events";
    
    public function __construct($em = NULL)
    {
        parent::__construct($em);
        
        $this->_modelEvents = new Events_Model_Events($this->_em);
    }
   
    public function insert()
    {
        $formData = $this->getForm()->getValues();
        
        $newCategoryId = parent::insert($formData);
        
        $this->_save($newCategoryId);
          
    }
    
    public function update(array $values, $categoryId)
    {
        parent::update($values, $categoryId);
        
        $this->_save($categoryId);
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
    
    public function delete($categoryId)
    {
        $category = $this->getStorage()->find($categoryId);
        if ($category)
        {
             $categoryName = $category->name;
            parent::delete($categoryId);


            //get category to erase
            //erase specific entity
            unlink($this->getContainerForRowsPath($categoryName));

            //updatedatabase
            Pepit_Doctrine_Tool::updateDoctrineSchemaForCreatingNewContainer(
                    $this->_em,
                    $this->getParentClassContainer()
            );
        }
        else
        {
            throw new Pepit_Model_Exception(
                    'No category to delete in the database'
            );
        }
    }
    
    public function createEntityFromForm() 
    {
        $formValues = $this->getForm()->getValues();  
        
        // create new category
        $category = new ZC\Entity\Category();
        $category->name = $formValues['name'];
        
        return $this->_saveEntityFromForm($category);
       
    }
    
    public function updateEntityFromForm(array $formValues,$entityId)
    {
        $formValues = $this->getForm()->getValues();  
        
        $category = $this->getStorage()->find($entityId);
        
        return $this->_saveEntityFromForm($formValues, $category);
    }
    
    protected function _saveEntityFromForm($category)
    {
        
        $formValues = $this->getForm()->getValues();
        
        if (array_key_exists('itemIds',$formValues) &&
                is_array($formValues['itemIds']))
        {
            $category->items = new \Doctrine\Common\Collections\ArrayCollection();
            foreach ($formValues['itemIds'] as $itemId)
            {
                $item = $this->_em->getRepository('ZC\Entity\GeneralizedItem')->find($itemId);
                $category->addItem($item);
            }
        }
        
        if (array_key_exists('categoryIds',$formValues) &&
                is_array($formValues['categoryIds']))
        {
            $category->categories = new \Doctrine\Common\Collections\ArrayCollection();
            foreach ($formValues['categoryIds'] as $categoryId)
            {
                $newCategoryForMeta = $this->_em->getRepository('ZC\Entity\Category')->find($categoryId);
                $category->addCategory($newCategoryForMeta);
            }
        }
        
        return $category;
    }
    

    static public function getNameSpaceForContainer()
    {
        return 'ZC\Entity\\'.self::CONTAINER_ENTITY_SUFFIX;
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
        return $initialSlash.'ZC\Entity\Event';
    }
    
    static public function getRowContainerEntityName($entityName)
    {
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        return 'ZC\Entity\\'.$suffix.'\\'.ucfirst($entityName).$suffix;
    }
    
    static public function getRowContainerShortEntityName($entityName)
    {
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        return ucfirst($entityName).$suffix;
    }
    
    /**
     * returns path for container or directory if null given
     * @param string $entityName
     * @return string 
     */
    
    static public function getContainerForRowsPath($entityName = NULL)
    {
        $suffix = self::CONTAINER_ENTITY_SUFFIX;
        $directory = APPLICATION_PATH.'/../library/ZC/Entity/'.$suffix.'/';
        if ($entityName === NULL)
        {
            return $directory;
        }
        return $directory.ucfirst($entityName).$suffix.'.php';
    }
    
    
    
    
    
}

