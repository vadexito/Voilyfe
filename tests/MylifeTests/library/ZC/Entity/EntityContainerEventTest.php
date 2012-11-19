<?php

/**
 *
 * @group Entities 
 */
class EntityContainerEventTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    protected $_className;
    
    protected $_event;
    
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
        
        $this->category = TestHelpersDoctrine::createCategoryTest(
            $this->em,
            self::$_categoryName,
            self::$_itemNames,
            false
        );
        
        
        $this->_className = 
                Backend_Model_Categories::getRowContainerEntityName(
                $this->category->name
        );
        
        $this->_event = TestHelpersDoctrine::createEventForCategoryTest(
            $this->em,
            new Events_Model_Events($this->em),
            $this->category,
            TestHelpersDoctrine::getMember($this->em,'dave','pass',true,true)
        );
    }
    
    public function testCanCreateTestEvent()
    {
        $event = $this->_event;
        
        $eventId = $event->id;
        $this->em->clear();
        
        $eventDB = $this->em->getRepository('ZC\Entity\Event')
                             ->find($eventId);
        
        $this->assertInstanceOf('ZC\Entity\Event',$eventDB);
        $this->assertInstanceOf($this->_className,$eventDB);
        
        foreach ($this->category->items as $item)
        {
            $propertyName = Events_Model_Events::getPropertyName(
                    $this->category->name,
                    $item->name
            );
        
            $this->assertSameItemPropertyForEvent(
                $item->associationType,
                $event->$propertyName,
                $eventDB->$propertyName
            );
        }
        
        $this->assertEquals($event->date,$eventDB->date);
        $this->assertInstanceOf('ZC\Entity\Member',$eventDB->member);
        $this->assertEquals($event->member->userName,$eventDB->member->userName);
        $this->assertEquals($event->creationDate,$eventDB->creationDate);
        $this->assertEquals($event->modificationDate,$eventDB->modificationDate);
        
    }  
    
    public function testCanRemoveTestEvent()
    {
        $eventId = $this->_event->id;
        $this->em->remove($this->_event);
        $this->em->flush();
        
        //check the event is not anymore in the tables
        $this->assertNull($this->em->getRepository('ZC\Entity\Event')
                             ->find($eventId));
        $this->assertNull($this->em->getRepository($this->_className)
                             ->find($eventId));
    }
}
