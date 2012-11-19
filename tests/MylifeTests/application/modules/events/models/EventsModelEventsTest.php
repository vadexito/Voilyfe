<?php


/**
 * @group Models 
 * @group Events
 * 
 */


class EventsModelEventsTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    protected $_repository;
    
    protected $_model;
    
    protected $member;
    
    static protected $_categoryName;
    static protected $_itemNames;

    
    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        //create the files for category aznd three items in test
        $category = TestHelpersDoctrine::createCategoryTest(self::$_em);
        self::$_categoryName = $category->name;
        
        self::$_itemNames = array(
            'string'    => $category->items[0]->name,
            'oneToMany' => $category->items[1]->name,
            'manyToOne' => $category->items[2]->name,
        );
        self::$_unlinkTearDownClass[] = 
            Backend_Model_Categories::getContainerForRowsPath($category->name);
        foreach (self::$_itemNames as $name)
        {
            self::$_unlinkTearDownClass[] = 
            Backend_Model_Items::getFormElementPath($name);
        }   
    }
    

    public function setUp()
    {
        parent::setUp();
        
        $this->category =  TestHelpersDoctrine::createCategoryTest(
            $this->em,
            self::$_categoryName,
            self::$_itemNames,
            false
        );
        
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
                $this->em,
            'userName',
            $this->memberPass,true,true
        );
        $this->_model = new Events_Model_Events();
        $this->_repository = $this->em->getRepository('ZC\Entity\Event');
        
    }
    
    public function testCreateEntityNewEventWithSingleItems()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $formNames = array();
        $propertyNames = array();
        foreach ($this->category->items as $item)
        {
            $propertyName = Events_Model_Events::getPropertyName(
                $this->category->name,
                $item->name
            );
            $propertyNames[] = $propertyName;
            $formNames[$item->associationType] = 
                Backend_Model_Items::getFormItemName(
                    $propertyName,
                    $item->associationType
            );
        }
        $formData = array(
            'categoryId'        => $this->category->id,
            'date'              => '01-01-2012',
            $formNames[0]       => 'testString',
            $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY]
                => 'value1,value2',
            $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE]
                => 1,
        );
        
        //insert new $user
        $event = $this->_model->createEntityFromForm($formData);
        $this->em->persist($event);
        $this->em->flush();
        $this->em->clear();
        
        $eventDB = $this->_repository->find($event->id);
        
        //check all elements
        $this->assertEquals($this->category->id,$eventDB->category->id);
        $this->assertEquals(new \dateTime('2012-01-01'),$eventDB->date);
        $this->assertEquals($this->member->userName,$eventDB->member->userName);
        
        $propertyNameString = $propertyNames[0];
        $propertyNameOneToMany = $propertyNames[1];
        $propertyNameManyToOne = $propertyNames[2];
        
        $formNameString = $formNames[0] ;
        $formNameOneToMany = $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY];
        $formNameManyToOne = $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE];
        
        $this->assertEquals($formData[$formNameString],$eventDB->$propertyNameString);
        
        $targetOneToMany = explode(',',$formData[$formNameOneToMany]);
        $this->assertEquals(count($targetOneToMany),$eventDB->$propertyNameOneToMany->count());
        foreach ($eventDB->$propertyNameOneToMany as $row)
        {
            $this->assertTrue(in_array($row->value,$targetOneToMany));
        }
        
        $this->assertEquals(
            $this->em->getRepository('ZC\Entity\ItemRow')->find(
                $formData[$formNameManyToOne]
            )->value,
            $eventDB->$propertyNameManyToOne->value
        );
        
        //check the date fields
        $this->assertInstanceOf('\DateTime',$eventDB->creationDate);
        $this->assertInstanceOf('\DateTime',$eventDB->modificationDate);
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$eventDB->creationDate->getTimeStamp()),10);
        $this->assertGreaterThan(abs($now->getTimestamp()-$eventDB->modificationDate->getTimeStamp()),10);
    }
    
    //@ML-TODO test to be finished
    public function estCreateNewEventWithItemGroup()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $itemGroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        
        $category = $this->category;
        
        
        
        
        $formNames = array();
        $propertyNames = array();
        foreach ($this->category->items as $item)
        {
            $propertyName = Events_Model_Events::getPropertyName(
                $this->category->name,
                $item->name
            );
            $propertyNames[] = $propertyName;
            $formNames[$item->associationType] = 
                Backend_Model_Items::getFormItemName(
                    $propertyName,
                    $item->associationType
            );
        }
        $formData = array(
            'categoryId'        => $this->category->id,
            'date'              => '01-01-2012',
            $formNames[0]       => 'testString',
            $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY]
                => 'value1,value2',
            $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE]
                => 1,
            'submit_insert'     => $this->vr->view->translate('action_save')
        );
        
        //insert new $user
        $event = $this->_model->createEntityFromForm($formData);
        $this->em->persist($event);
        $this->em->flush();
        $this->em->clear();
        
        $eventDB = $this->_repository->find($event->id);
        
        //check all elements
        $this->assertEquals($this->category->id,$eventDB->category->id);
        $this->assertEquals(new \dateTime('2012-01-01'),$eventDB->date);
        $this->assertEquals($this->member->userName,$eventDB->member->userName);
        
        $propertyNameString = $propertyNames[0];
        $propertyNameOneToMany = $propertyNames[1];
        $propertyNameManyToOne = $propertyNames[2];
        
        $formNameString = $formNames[0] ;
        $formNameOneToMany = $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY];
        $formNameManyToOne = $formNames[\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE];
        
        $this->assertEquals($formData[$formNameString],$eventDB->$propertyNameString);
        
        $targetOneToMany = explode(',',$formData[$formNameOneToMany]);
        $this->assertEquals(count($targetOneToMany),$eventDB->$propertyNameOneToMany->count());
        foreach ($eventDB->$propertyNameOneToMany as $row)
        {
            $this->assertTrue(in_array($row->value,$targetOneToMany));
        }
        
        $this->assertEquals(
            $this->em->getRepository('ZC\Entity\ItemRow')->find(
                $formData[$formNameManyToOne]
            )->value,
            $eventDB->$propertyNameManyToOne->value
        );
        
        //check the date fields
        $this->assertInstanceOf('\DateTime',$eventDB->creationDate);
        $this->assertInstanceOf('\DateTime',$eventDB->modificationDate);
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$eventDB->creationDate->getTimeStamp()),10);
        $this->assertGreaterThan(abs($now->getTimestamp()-$eventDB->modificationDate->getTimeStamp()),10);
    }
 
    
    protected function createEntityManagerMock()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
    
    
    /**
     *
     * 
     * @dataProvider providerItemTypes
     */
    public function testHydrateForSingleProperty(
            $formValues,$fieldMappings,$associationMappings,$target)
    {
        //mock metadata
        $metaDataMock = new stdClass();
        $metaDataMock->fieldMappings = $fieldMappings;
        $metaDataMock->associationMappings = $associationMappings;
        
        $metadataFactoryMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataFactory')
                        ->disableOriginalConstructor()
                        ->getMock();
        
        $repositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                        ->disableOriginalConstructor()
                        ->getMock();
        
        if (is_object($target) && get_class($target) === 'stdClass')
        {
             $itemRowMock1 = $target;
             $itemRowMock2 = $target;
             $itemRowMock3 = $target;
        }
        else
        {
            $itemRowMock1 = $target[0];
            $itemRowMock2 = $target[1];
            $itemRowMock3= $target[2];
        }
       
        
        $emMock = $this ->getMockBuilder('Doctrine\ORM\EntityManager')
                        ->disableOriginalConstructor()
                        ->getMock();
        $emMock ->expects($this->any())
                ->method('getMetadataFactory')
                ->will($this->returnValue($metadataFactoryMock));
        $emMock ->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($repositoryMock));
        $metadataFactoryMock ->expects($this->any())
                ->method('getMetadataFor')
                ->will($this->returnValue($metaDataMock));
        $repositoryMock ->expects($this->any())
                ->method('find')
                ->will($this->onConsecutiveCalls(
                        $itemRowMock1,
                        $itemRowMock2,
                        $itemRowMock3
                ));
        
        //get model
        $model = new Events_Model_Events($emMock);
        
        if (!empty($fieldMappings))
        {
            $keys = array_keys($fieldMappings);
            $propertyKey = $keys[0];
        }
        if (!empty($associationMappings))
        {
            $keys = array_keys($associationMappings);
            $propertyKey = $keys[0];
        }
        
        $generalizedItemMock = new GeneralizedItemMock();
        $generalizedItemMock->$propertyKey = NULL;
        
        $entity = $model->saveEntityFromForm($formValues,$generalizedItemMock);
        
        //check hydratation
        if (is_object($target) && get_class($target) === 'Doctrine\Common\Collections\ArrayCollection')
        {
            $i=0;
            $collection = $entity->$propertyKey;
                        
            foreach ($target as $value)
            {
                $this->assertEquals($value->value,$collection[$i]->value);
                $i++;
            }
        }
        elseif(is_object($target))
        {
            $this->assertEquals($entity->$propertyKey->value,$target->value);
        }
        else
        {
            $this->assertEquals($entity->$propertyKey,$target);
        }
        
        
    }
    
    public function providerItemTypes()
    {
        $metadata1 = array(
            'type'          => 2, //many to one
            'targetEntity'  => 'mocked'
        );
        $idItemRow1 = 1;
        $propertyKey1 = 'key';
        $formKey1 = 'keyId';
        
        $formValues1 = array( $formKey1 => $idItemRow1);
        $fieldMappings1 = array();
        $associationMappings1 = array($propertyKey1 => $metadata1);
        
        $target1 = new stdClass();
        $target1->value = 'value';
        $target1->id = $idItemRow1;
        
        $metadata2 = array(
            'type'          => 8,
            'targetEntity'  => 'stdClass', //one to many
            'joinTable'    => 'mocked'
        );
        $propertyKey2 = 'keys';
        $formKey2 = 'keys';
        $values2 = 'value1,value2,value3';
        
        $formValues2 = array( $formKey2=> $values2);
        $fieldMappings2 = array();
        $associationMappings2 = array($propertyKey2 => $metadata2);
        $target2 = new \Doctrine\Common\Collections\ArrayCollection();
        for ($i=0;$i<2;$i++)
        {
            $stdClass = new stdClass();
            $stdClass->value = 'value'.($i+1);
            $stdClass->creationDate = new Datetime;
            $stdClass->modificationDate = new Datetime;
            $target2[] = $stdClass;
        }
        
        $metadata3 = array(
            'type'          => 8,
            'targetEntity'  => '' //many to many
        );
        $propertyKey3 = 'keys';
        $formKey3 = 'keyIds';
        $values3 = array(1,2,3);
        $formValues3 = array( $formKey3 => $values3);
        $fieldMappings3 = array();
        $associationMappings3 = array($propertyKey3 => $metadata3);
        
        $target31 = new stdClass();
        $target31->value = 'value1';
        $target31->id = 1;
        $target32 = new stdClass();
        $target32->value = 'value2';
        $target32->id = 2;
        $target33 = new stdClass();
        $target33->value = 'value3';
        $target33->id = 3;
        $target3 = new \Doctrine\Common\Collections\ArrayCollection();
        $target3[] = $target31;
        $target3[] = $target32;
        $target3[] = $target33;
        
        $metadata4 = array(
            'type'          => 1, //one to one
            'targetEntity'  => 'stdClass'
        );
        
        $propertyKey4 = 'key';
        $formKey4 = 'key';
        $values4 = 'value1';
        
        $formValues4 = array( $formKey4 => $values4);
        $fieldMappings4 = array();
        $associationMappings4 = array($propertyKey4 => $metadata4);
        $target4 = new stdClass();
        $target4->value = 'value1';
        $target4->creationDate = new Datetime;
        $target4->modificationDate = new Datetime;
        
        
        return array(
            array(array('key' => 'value1'),array('key' => 'data'),array(),'value1'),
            array($formValues1,$fieldMappings1,$associationMappings1,$target1),
            array($formValues2,$fieldMappings2,$associationMappings2,$target2),
            array($formValues3,$fieldMappings3,$associationMappings3,$target3),
            array($formValues4,$fieldMappings4,$associationMappings4,$target4),

        );
    }
}

class GeneralizedItemMock extends stdClass
{

    public $keys = null;
    
    public function __construct()
    {
        $this->keys = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function addKey($mock)
    {
        $this->keys[] = $mock;
    }

}
