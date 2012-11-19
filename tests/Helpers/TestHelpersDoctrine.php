<?php

/**
 *
 * file_name
 * 
 * @package Mylife
 * @author DM 
 */

class TestHelpersDoctrine 
{
    /**
     * initialize database for testing
     *  
     */
    static public function initDoctrineSchema($em)
    {
        // define tool cli for doctrine and execute create entity
        $tool = new Doctrine\ORM\Tools\SchemaTool($em);
        
        //get metadata from entities
        $metas = $em->getMetadataFactory()->getAllMetadata();
              
        //drop existing database
        self::dropDoctrineSchema($em);
        
        // recreate entities
        $tool->createSchema($metas);
    }
        
    /**
     * remove database datas
     *  
     */ 
    
    static public function dropDoctrineSchema($em)
    {
        // define tool cli for doctrine and execute create entity
        $tool = new Doctrine\ORM\Tools\SchemaTool($em);
        
        //drop existing database
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
    }
    
    
    /**
     * return a new Entity with property in an array
     * 
     * @param type $EntityName
     * @param array $properties
     * @param type $persist
     * @param type $flush
     * @return \EntityName 
     */
    
    static public function getEntity($em,$EntityName,Array $properties=NULL,
                                                $persist=false,$flush=false)
    {
        $entity = new $EntityName();
        
        if ($properties)
        {
            self::hydrateEntityFromArray($em,$entity,$properties,$persist);
        }
        
        // if no persist also no flush
        if ($persist)
        {
            $em->persist($entity);
            if($flush)
            {
                $em->flush();
            }
        }
        return $entity;
    }       
            
            
    /**
     * create new member in the database
     * 
     * @return \ZC\Entity\Member 
     */
    static public function getMember($em,$name,$pass,$persist=false,$flush=false)
    {
        $hash = new Pepit_Auth_Hash;
        $salt = 'fgQ2jtpAIBAjX4fQl8MGoKMGHsFkUuRI';
        
        $properties = array(
            'firstName'         => 'firstname',
            'lastName'          => 'lastname',
            'userName'          => $name,
            'userPassword'      => $hash->hashPassword($pass, $salt),
            'passwordSalt'      => $salt,
            'email'             => 'first.last@test.org',
            'registeringDate'   => new DateTime(),
            'role'              => 'member',
            'country'           => self::getEntity($em,'ZC\Entity\ItemMulti\Country',array('value'=>'FR'),true,false),
            'language'          => self::getEntity($em,'ZC\Entity\ItemMulti\Language',array('value'=>'fr'),true,false)
        );
        
        return self::getEntity($em,'ZC\Entity\Member',$properties,$persist,$flush);
    }
    
    static public function initUserRegisterItems($em)
    {
        $countryFixture = array(
            array('value'=>'FR'),
            array('value'=>'GE'),
            array('value'=>'PL'),
            array('value'=>'GB'),
        );
        
        $languageFixture = array(
            array('value'=>'fr_FR'),
            array('value'=>'de_DE'),
            array('value'=>'en_GB'),
            array('value'=>'pl_PL'),
        );
        
        self::createEntitiesFromFixture(
            $em,
            'ZC\Entity\ItemMulti\Country',
            $countryFixture
        );
        
        self::createEntitiesFromFixture(
            $em,
            'ZC\Entity\ItemMulti\Language',
            $languageFixture
        );
        
        $em->flush();
    }
    
       
    static public function getItemRow($value,$item = NULL)
    {
        $itemRow = new ZC\Entity\ItemRow();
        $itemRow->creationDate = new \DateTime();
        $itemRow->modificationDate = new \DateTime();
        $itemRow->value = $value;
        $itemRow->item = $item;
        return $itemRow;
    }
    
    static public function initBaseFormItems($em)
    {
        self::createFormElementClasses($em);
        self::createFormFilters($em);
        self::createFormValidators($em);
    }
    
    static public function createFormElementClasses($em)
    {
        try
        {
            $storage = new Zend_Config_Xml(
                APPLICATION_PATH.'/modules/backend/Data/Init/InitialData.xml',
                'init'
            );
            
            $fixture = array();
            foreach($storage->formElementClass->type as $value)
            {
                $fixture[] = array('name'=> $value->value);
            }
        } 
        catch (Exception $exc)
        {
            throw new Pepit_Test_Exception(
                    'Not possible to initialize form element classes because of'
                    .$exc->getMessage()
            );
        }

        self::createEntitiesFromFixture(
            $em,
            '\ZC\Entity\FormElementClass',
            $fixture
        );
        
        $em->flush();
    }
    
         
    static public function createFormFilters($em)
    {
        $fixture = array(
            array('name' => 'StringTrim'),
            array('name' => 'StripTags'),
            array('name' => 'HTMLEntities')
        );
        
        self::createEntitiesFromFixture(
            $em,
            '\ZC\Entity\FormFilter',
            $fixture
        );
        
        $em->flush();
    }
    
    
    static public function createFormValidators($em)
    {
        $fixture = array(
            array('name' => 'NotEmpty' ),
            array('name' => 'Int')
        );
        
        self::createEntitiesFromFixture(
            $em,
            '\ZC\Entity\FormValidator',
            $fixture
        );
        
        $em->flush();
    }
    
    static protected function createEntitiesFromFixture($em,$entityName,array $fixtures)
    {
        foreach ($fixtures as $fixture)
        {
            $fixture = each($fixture);
            $entity = new $entityName();
            $property = $fixture[0];
            $value = $fixture[1];
            
            $entity->$property = $value;
            $em->persist($entity);
        }
    }
    /**
     * create new item associated in one to many with the property event
     * 
     * @return \ZC\Entity\Item 
     */
    
    static public function createItemOneToMany($em,$name = NULL)
    {
        //define general properties
        if ($name == NULL)
        {
            //name (plural form)
            $name = 'OneToMany'.'s'; 
            $name = self::addTestFilePrefix($name);
        }
        $properties = array(
            'name'              => $name,
            'typeSQL'           => 'string',
            'sizeSQL'           => 255,
            'nullableSQL'       => '0',
            'formLabel'         => 'testOneToManies (enter values separated by commas)',
            'formRequired'      => '1',
            'associationType'   => \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
        );
        
        //define entity
        $item =  self::getEntity($em,'\ZC\Entity\Item',$properties);
        
        // define class for form element
        $classForm = $em->getRepository('ZC\Entity\FormElementClass')
                              ->findOneByName('Pepit_Form_Element_Text');
        $item->formElementClass = $classForm;
         
        //persist item and flush 
        $em->persist($item);        
        $em->flush();
        
        return $item;
        
    }
    
    /**
     * item OneToOne  
     * @param string $name
     * @return type 
     */
    static public function createItemOneToOne($em,$name = NULL)
    {
        //define general properties
        if ($name == NULL)
        {
            $name = 'testOneToOne'; 
            $name = self::addTestFilePrefix($name);
        }
        $properties = array(
            'name'              => $name,
            'typeSQL'           => 'string',
            'sizeSQL'           => 255,
            'nullableSQL'       => '0',
            'formLabel'         => 'testOneToOne',
            'formRequired'      => '1',
            'associationType'   => \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE
        );
        
        //define entity
        $item =  self::getEntity($em,'\ZC\Entity\Item',$properties);
        
        //define multioption
        $item->addItemRow(self::getItemRow('value1',$item));
        $item->addItemRow(self::getItemRow('value2',$item));
        $item->addItemRow(self::getItemRow('value3',$item));
        
        // define class for form element
        $classForm = $em->getRepository('ZC\Entity\FormElementClass')
                              ->findOneByName('Pepit_Form_Element_Select');
        $item->formElementClass = $classForm;
         
        //persist item and flush 
        $em->persist($item);        
        $em->flush();
        
        return $item;
    }
    static public function createItemManyToMany($em,$name = NULL)
    {
        //define general properties
        if ($name == NULL)
        {
            //name (plural form)
            $name = Pepit_Inflector::pluralize('ManyToMany'); 
            $name = self::addTestFilePrefix($name);
        }
        $properties = array(
            'name'              => $name,
            'typeSQL'           => 'string',
            'sizeSQL'           => 255,
            'nullableSQL'       => '0',
            'formLabel'         => 'testManyToManies',
            'formRequired'      => '1',
            'associationType'   => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
        );
        
        //define entity
        $item =  self::getEntity($em,'\ZC\Entity\Item',$properties);
        
        //define multioption
        $item->addItemRow(self::getItemRow('value1',$item));
        $item->addItemRow(self::getItemRow('value2',$item));
        $item->addItemRow(self::getItemRow('value3',$item));
        
        // define class for form element
        $classForm = $em->getRepository('ZC\Entity\FormElementClass')
                              ->findOneByName('Pepit_Form_Element_MultiCheckbox');
        $item->formElementClass = $classForm;
         
        //persist item and flush 
        $em->persist($item);        
        $em->flush();
        
        return $item;
        
    }
    
     /**
     * create new item with multioption
     * 
     * @return \ZC\Entity\Member 
     */
    
    static public function createItemManyToOne($em,$name = NULL)
    {
        //define general properties
        if ($name == NULL)
        {
            $name = 'ManyToOne';
            $name = self::addTestFilePrefix($name);
        }
        $properties = array(
            'name'              => $name,
            'typeSQL'           => 'string',
            'sizeSQL'           => 15,
            'nullableSQL'       => '0',
            'formLabel'         => 'testManyToOne',
            'formRequired'      => '1',
            'associationType'   => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE 
        );
        
        //define entity
        $item =  self::getEntity($em,'\ZC\Entity\Item',$properties);
        
        //define multioption
        $item->addItemRow(self::getItemRow('value1',$item));
        $item->addItemRow(self::getItemRow('value2',$item));
        $item->addItemRow(self::getItemRow('value3',$item));
        
        // define class for form element
        $classForm = $em->getRepository('ZC\Entity\FormElementClass')
                              ->findOneByName('Pepit_Form_Element_Select');
        $item->formElementClass = $classForm;
         
        //persist item and flush 
        $em->persist($item);        
        $em->flush();
        
        return $item;
        
    }
    
   
    /**
     * create new item with no multioption
     * 
     * @return \ZC\Entity\Member 
     */
    
    static public function createItemString($em,$name = NULL,$createFiles = false)
    {
        
        //define form element class
        $classForm = $em->getRepository('ZC\Entity\FormElementClass')
                              ->findOneByName('Pepit_Form_Element_Text');
        //define properties
        if ($name == NULL)
        {
            $name = 'string';
            $name = self::addTestFilePrefix($name);
        }
        $properties = array(
            'name'              => $name,
            'typeSQL'           => 'string',
            'sizeSQL'           => 255,
            'nullableSQL'       => true,
            'formLabel'         => 'testString',
            'formRequired'      => '0',
            'associationType'   => '0'
        );
        
        //define entity
        $item =  self::getEntity($em,'\ZC\Entity\Item',$properties);
        
        // define class for form element        
        $item->formElementClass = $classForm;
             
        //persist item and flush 
        $em->persist($item);        
        $em->flush();
        
        if ($createFiles === true)
        {
            $model = new Backend_Model_Items($em);
            $model->createNewFormElement($item);
        }
        
        return $item;
    }
    
   
   
    /**
     * create new category
     * 
     * @return \ZC\Entity\Category
     */
    
    static public function createCategoryTest($em,$categoryName=NULL,
                                    array $itemNames = array(),$createfiles=true)
    {
        if ($categoryName === NULL)
        {
            $categoryName = 'Cat';
            $categoryName = self::addTestFilePrefix($categoryName);
        }
        
        $properties = array(
            'name'          => $categoryName,
        );
        
        self::initBaseFormItems($em);
        
        //create category and add item
        $category =  self::getEntity($em,'\ZC\Entity\Category',$properties);
        
        if ($itemNames === array())
        {
            $string = self::createItemString($em);
            $oneToMany = self::createItemOneToMany($em);
            $manyToOne = self::createItemManyToOne($em);
            $category->addItem($string); 
            $category->addItem($oneToMany);          
            $category->addItem($manyToOne);  
        } 
        elseif (array_key_exists('string',$itemNames) &&
                array_key_exists('oneToMany',$itemNames) &&
                array_key_exists('manyToOne',$itemNames))
        {
            $string = self::createItemString($em,$itemNames['string']);
            $category->addItem($string); 
            $oneToMany = self::createItemOneToMany($em,$itemNames['oneToMany']);
            $category->addItem($oneToMany); 
            $manyToOne = self::createItemManyToOne($em,$itemNames['manyToOne']);
            $category->addItem($manyToOne);   
        }
        else
        {
            throw new Pepit_Test_Exception('Bad Type of Data in function');
        }
       
        
        $em->persist($category);
        $em->flush();
        
        if ($createfiles)
        {
            //create new entity
            $model = new Backend_Model_Categories($em);
            $model->createContainerEntity($category);
            
            $model = new Backend_Model_Items($em);
            $model->createNewFormElement($string);
            $model->createNewFormElement($oneToMany);
            $model->createNewFormElement($manyToOne);
        }
        
        return $category;
    }
    
    /**
     * create new category
     * 
     * @return \ZC\Entity\Category
     */
    
    static public function createCategoryWithItems(
        $em,array $generalizedItems,$categoryName=NULL,$createfiles=true)
    {
        if ($categoryName === NULL)
        {
            $categoryName = 'Cat';
            $categoryName = self::addTestFilePrefix($categoryName);
        }
        
        $properties = array('name' => $categoryName);
        
        
        //create category and add item
        $category =  self::getEntity(
                $em,
                '\ZC\Entity\Category',
                $properties
        );
        
        foreach ($generalizedItems as $item)
        {
            $category->addItem($item);
        }
        
        $em->persist($category);
        $em->flush();
        
        if ($createfiles)
        {
            //create new entity
            $model = new Backend_Model_Categories($em);
            $model->createContainerEntity($category);
        }
        
        return $category;
    }
    
    /**
     * create new category
     * 
     * @return \ZC\Entity\Category
     */
    
    static public function createItemGroupWithItems(
        $em,array $generalizedItems,$identifierItem,$itemGroupName=NULL,$createfiles=true)
    {
        if ($itemGroupName === NULL)
        {
            $itemGroupName = 'ItG';
            $itemGroupName = self::addTestFilePrefix($itemGroupName);
        }
        
        $properties = array(
            'name'              => $itemGroupName,
            'formRequired'      => '1',
            'associationType'   => Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE,
            'itemType'          => ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP,
            'formElementClass'  => $em->getRepository('ZC\Entity\FormElementClass')
                                    ->findOneByName('Pepit_Form_Element_Select'),
        );
        
        //create category and add item
        $itemGroup =  self::getEntity(
                $em,
                '\ZC\Entity\ItemGroup',
                $properties
        );
        
        foreach ($generalizedItems as $item)
        {
            $itemGroup->addItem($item); 
        }
        
        $itemGroup->identifierItem = $identifierItem;
        
        $em->persist($itemGroup);
        $em->flush();
        
        if ($createfiles)
        {
            //create new entity
            $model = new Backend_Model_ItemGroups($em);
            $model->createContainerEntity($itemGroup);
        }
        
        return $itemGroup;
    }
    
    static public function createItemGroupTest($em,$itemGroupName=NULL,
                                    array $itemNames = array(),$createfiles=true)
    {
        if ($itemGroupName === NULL)
        {
            $itemGroupName = 'IG';
            $itemGroupName = self::addTestFilePrefix($itemGroupName);
        }
        
        $properties = array(
            'name'              => $itemGroupName,
            'formRequired'      => '1',
            'associationType'   => Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY,
            'itemType'          => ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP,
            'formElementClass'  => $em->getRepository('ZC\Entity\FormElementClass')
                                    ->findOneByName('Pepit_Form_Element_Select'),
        );
        
        self::initBaseFormItems($em);
        
        //create category and add item
        $itemGroup =  self::getEntity($em,'\ZC\Entity\ItemGroup',$properties);
        
        if ($itemNames === array())
        {
            $string = self::createItemString($em);
            $oneToMany = self::createItemOneToMany($em);
            $manyToOne = self::createItemManyToOne($em);
            $itemGroup->addItem($string); 
            $itemGroup->addItem($oneToMany);          
            $itemGroup->addItem($manyToOne);  
        } 
        elseif (array_key_exists('string',$itemNames) &&
                array_key_exists('oneToMany',$itemNames) &&
                array_key_exists('manyToOne',$itemNames))
        {
            $string = self::createItemString($em,$itemNames['string']);
            $itemGroup->addItem($string); 
            $oneToMany = self::createItemOneToMany($em,$itemNames['oneToMany']);
            $itemGroup->addItem($oneToMany); 
            $manyToOne = self::createItemManyToOne($em,$itemNames['manyToOne']);
            $itemGroup->addItem($manyToOne);   
        }
        else
        {
            throw new Pepit_Test_Exception('Bad Type of Data in function');
        }
        $itemGroup->identifierItem = $string;
       
        
        $em->persist($itemGroup);
        $em->flush();
        
        if ($createfiles)
        {
            //create new entity
            $model = new Backend_Model_ItemGroups($em);
            $model->createContainerEntity($itemGroup);
            
            $model = new Backend_Model_Items($em);
            $model->createNewFormElement($string);
            $model->createNewFormElement($oneToMany);
            $model->createNewFormElement($manyToOne);
        }
        
        return $itemGroup;
    }
    
    static public function createItemGroupTestWithOneItem(
        $em,$item,$itemGroupName=NULL,$createfiles=true)
    {
        if ($itemGroupName === NULL)
        {
            $itemGroupName = 'IG';
            $itemGroupName = self::addTestFilePrefix($itemGroupName);
        }
        
        $properties = array(
            'name'              => $itemGroupName,
            'formRequired'      => '1',
            'associationType'   => Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY,
            'itemType'          => ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP,
            'formElementClass'  => $em->getRepository('ZC\Entity\FormElementClass')
                                    ->findOneByName('Pepit_Form_Element_Select'),
        );
        
        self::initBaseFormItems($em);
        
        //create category and add item
        $itemGroup =  self::getEntity($em,'\ZC\Entity\ItemGroup',$properties);
        
        $itemGroup->addItem($item); 
        
        $itemGroup->identifierItem = $item;
        
        $em->persist($itemGroup);Doctrine\Common\Util\Debug::dump($itemGroup);die;
        $em->flush();
        
        if ($createfiles)
        {
            //create new entity
            $model = new Backend_Model_ItemGroups($em);
            $model->createContainerEntity($itemGroup);
            
            $model = new Backend_Model_Items($em);
            $model->createNewFormElement($string);
          
        }
        
        return $itemGroup;
    }
    
    
    static public function createEventForCategoryTest($em,
        $modelEvent,\ZC\Entity\Category $category,\ZC\Entity\Member $member)
    {
        $className = Backend_Model_Categories::getRowContainerEntityName(
            $category->name
        );
        $event = new $className;
        
        $event->date = new DateTime('2001-02-03');
        $event->member = $member;
        $event->creationDate = new \DateTime();
        $event->modificationDate =  $event->creationDate;        
        $event->category = $category;
        
        $stringName = $modelEvent->getPropertyName(
                    $category->name,
                    $category->items[0]->name
            );
        $oneToManyName = $modelEvent->getPropertyName(
                    $category->name,
                    $category->items[1]->name
            );
        $manyToOneName = $modelEvent->getPropertyName(
                    $category->name,
                    $category->items[2]->name
            );
        
        //add define item properties to event
        $formOptionOneToMany1 = $em->getRepository('ZC\Entity\ItemRow')
                            ->find(2);
        $formOptionOneToMany2 = $em->getRepository('ZC\Entity\ItemRow')
                            ->find(3);
        $formOptionManyToOne = self::getItemRow('valueForManyToOne',$category->items[2]);
           
                           
        $addMethod = 'add'.ucfirst(Pepit_Inflector::singularize($oneToManyName));
        
        $event->$stringName = 'testString';
        $event->$addMethod($formOptionOneToMany1);
        $event->$addMethod($formOptionOneToMany2);        
        $event->$manyToOneName = $formOptionManyToOne;
        
        $em->persist($formOptionManyToOne);
        $em->persist($event);
        $em->flush();
        
        return $event;
    }
    
    static public function createEventForCategory(
        $em,\ZC\Entity\Category $category,
            array $valuesOnItemProperties,\ZC\Entity\Member $member)
    {
        $className = Backend_Model_Categories::getRowContainerEntityName(
            $category->name
        );
        $event = new $className;
        
        $event->date = new DateTime('2001-02-03');
        $event->member = $member;
        $event->creationDate = new \DateTime();
        $event->modificationDate =  $event->creationDate;        
        $event->category = $category;
        
        foreach($valuesOnItemProperties as $property => $value)
        {
            $event->$property = $value;
        }
        
        $em->persist($event);
        $em->flush();
        
        return $event;
    }
    
    static public function createItemGroupRowForItemGroup(
        $em,  \ZC\Entity\ItemGroup $itemGroup,
            array $valuesOnItemProperties,\ZC\Entity\Member $member = NULL)
    {
        $className = Backend_Model_ItemGroups::getRowContainerEntityName(
            $itemGroup->name
        );
        $itemGroupRow = new $className;
        
        $itemGroupRow->member = $member;
        $itemGroupRow->creationDate = new \DateTime();
        $itemGroupRow->modificationDate =  $itemGroupRow->creationDate;        
        $itemGroupRow->itemGroup = $itemGroup;
        
        foreach($valuesOnItemProperties as $property => $value)
        {
            $itemGroupRow->$property = $value;
        }
        
        $em->persist($itemGroupRow);
        $em->flush();
        
        return $itemGroupRow;
    }
    
    static public function createItemGroupRowForItemGroupTest($em,
        $modelItemGroupRows,\ZC\Entity\ItemGroup $itemGroup,\ZC\Entity\Member $member)
    {
        $className = Backend_Model_ItemGroups::getRowContainerEntityName(
            $itemGroup->name
        );
        $itemGroupRow = new $className;
        
        $itemGroupRow->member = $member;
        $itemGroupRow->creationDate = new \DateTime();
        $itemGroupRow->modificationDate =  $itemGroupRow->creationDate;        
        $itemGroupRow->itemGroup = $itemGroup;
        
        $stringName = $modelItemGroupRows->getPropertyName(
                    $itemGroup->name,
                    $itemGroup->items[0]->name
            );
        $oneToManyName = $modelItemGroupRows->getPropertyName(
                    $itemGroup->name,
                    $itemGroup->items[1]->name
            );
        $manyToOneName = $modelItemGroupRows->getPropertyName(
                    $itemGroup->name,
                    $itemGroup->items[2]->name
            );
        
        //add define item properties to event
        $formOptionOneToMany1 = $em->getRepository('ZC\Entity\ItemRow')
                            ->find(2);
        $formOptionOneToMany2 = $em->getRepository('ZC\Entity\ItemRow')
                            ->find(3);
        $formOptionManyToOne = self::getItemRow('valueForManyToOne',$itemGroup->items[2]);
       
                           
        $addMethod = 'add'.ucfirst(Pepit_Inflector::singularize($oneToManyName));
        
        $itemGroupRow->$stringName = 'testString';
        $itemGroupRow->$addMethod($formOptionOneToMany1);
        $itemGroupRow->$addMethod($formOptionOneToMany2);        
        $itemGroupRow->$manyToOneName = $formOptionManyToOne;
        
        $em->persist($formOptionManyToOne);
        $em->persist($itemGroupRow);
        $em->flush();
        
        return $itemGroupRow;
    }
    
    static public function createItemGroupRowForItemGroupWithOneItem($em,
        $modelItemGroupRows,\ZC\Entity\ItemGroup $itemGroup,\ZC\Entity\Member $member)
    {
        $className = Backend_Model_ItemGroups::getRowContainerEntityName(
            $itemGroup->name
        );
        $itemGroupRow = new $className;
        
        $itemGroupRow->member = $member;
        $itemGroupRow->creationDate = new \DateTime();
        $itemGroupRow->modificationDate =  $itemGroupRow->creationDate;        
        $itemGroupRow->itemGroup = $itemGroup;
        
        $stringName = $modelItemGroupRows->getPropertyName(
                    $itemGroup->name,
                    $itemGroup->items[0]->name
            );
        $itemGroupRow->$stringName = 'testString';
        
        $em->persist($itemGroupRow);
        $em->flush();
        
        return $itemGroupRow;
    }

        
    
    /**
     * hydrate an entity with the properties contained in an array
     * @param Doctrine\ORM\Mapping\Entity $entity
     * @param array $properties
     * @param type $persist
     * @return \Doctrine\ORM\Mapping\Entity 
     */
    static public function hydrateEntityFromArray($em,$entity,Array $properties,$persist=true)
    {
        foreach($properties as $property => $value)
        {
            $entity->$property = $value;
        }
        if ($persist)
        {
            $em->persist($entity);
        }
        return $entity;
    }
    
    static public function addTestFilePrefix($name = '')
    {
        if ($name === '')
        {
            return Pepit_Test_ControllerTestCaseWithDoctrine::TEST_FILE_PREFIX;
        }
        $code = rand(1000,9999);
        return Pepit_Test_ControllerTestCaseWithDoctrine::TEST_FILE_PREFIX
                .$code.$name;
    }
    
    /**
     * unlink prefixed unittest files created
     * @param mixed $directoryPaths can be array of directory or directory name 
     */
    static public function unlinkTestFiles($directoryPath)
    {
        if (is_array($directoryPath))
        {
            foreach ($directoryPath as $dir)
            {
                self::unlinkTestFiles($dir);
            }
        }
        else
        {
            $prefix = self::addTestFilePrefix();
            $dir = opendir($directoryPath);
            if ($dir)
            {
                while($file = readdir($dir))
                {
                    $pattern = '#^'.$prefix.'(.*).php$#i';
                    if (preg_match($pattern,$file))
                    {
                        unlink($directoryPath.$file);
                    }
                }
            }
            else
            {
                throw new Pepit_Test_Exception(
                        'Invalid directory for unlinking files.'
                );
            }
            
        }
        
        
        
    }
    
    
    
    
}


