<?php

/**
 * @group Controllers
 * @group Events
 * 
 */
class EventControllerCategoryWithItemGroupTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    static protected $names;
    
    protected $category;
    protected $itemGroup;
    protected $item;

    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        TestHelpersDoctrine::initBaseFormItems(self::$_em);
        $item = TestHelpersDoctrine::createItemString(self::$_em, NULL, true);
        
        $itemGroup = TestHelpersDoctrine::createItemGroupWithItems(
                static::$_em,array($item),$item
        );
        
        $category = TestHelpersDoctrine::createCategoryWithItems(
                static::$_em,array($itemGroup)
        );
        
        
        array_push(
            static::$_unlinkTearDownClass, 
            Backend_Model_Categories::getContainerForRowsPath(
                    $category->name
            ),
            Backend_Model_ItemGroups::getContainerForRowsPath(
                    $itemGroup->name
            ),
            Backend_Model_Items::getFormElementPath(
                    $item->name
            )
        );
        
        self::$names = array(
            'category'  => $category->name,
            'itemGroup' => $category->items[0]->name,
            'item'      => $category->items[0]->items[0]->name,
        );
        
        
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
            $this->em, 
            'userName',
            $this->memberPass,true,true
        );
        
        //login
        $this->loginUser($this->member->userName,$this->memberPass);
        
        TestHelpersDoctrine::initBaseFormItems($this->em);
        
        //recreate elements in the database (files already exist)
        $this->item = TestHelpersDoctrine::createItemString(
                $this->em,
                self::$names['item'],
                false
        );
        
        $this->itemGroup = TestHelpersDoctrine::createItemGroupWithItems(
            $this->em,
            array($this->item),
            $this->item,
            self::$names['itemGroup'],
            false
        );
        
        $this->category = TestHelpersDoctrine::createCategoryWithItems(
                $this->em,
                array($this->itemGroup),
                self::$names['category'],
                false
        );
    }
    
    public function testEditEventWithOneItemGroupPostSentSuccessful()
    {
       
        
        $propertyItem = Events_Model_ItemGroupRows::getPropertyName(
            $this->itemGroup->name,
            $this->item->name
        );
        
        
        $propertyItemGroup = Events_Model_ItemGroupRows::getPropertyName(
                $this->category->name,
                $this->itemGroup->name
        );
        
        $valuesItemGroupRowOnItemProperties = array(
            $propertyItem => 'value1'
        );
        $itemGroupRow1 = TestHelpersDoctrine::createItemGroupRowForItemGroup(
                $this->em,
                $this->itemGroup,
                $valuesItemGroupRowOnItemProperties,
                $this->member
        );
        $valuesItemGroupRowOnItemProperties = array(
            $propertyItem => 'value2'
        );
        $itemGroupRow2 = TestHelpersDoctrine::createItemGroupRowForItemGroup(
                $this->em,
                $this->itemGroup,
                $valuesItemGroupRowOnItemProperties,
                $this->member
        );
        
        $valuesEventOnItemProperties = array(
             $propertyItemGroup => $itemGroupRow1
         );
        
        $event = TestHelpersDoctrine::createEventForCategory(
                $this->em,
                $this->category,
                $valuesEventOnItemProperties,
                $this->member
        );
        
        $formKey = Backend_Model_ItemGroups::getFormItemName(
            $propertyItemGroup,
            $this->itemGroup->associationType
        );
        
        $input = array(
            'categoryId'        => $this->category->id,
            'date'              => '01-01-2010',
            $formKey            => $itemGroupRow2->id,
            'submit_update'     => $this->vr->view->translate('action_save')
        );
        
        $this->request->setMethod('POST');
        $this->request->setPost($input);
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action'            => 'edit',
                'containerId'       => $this->category->id,
                'containerRowId'    => $event->id,
            ),
            'event')
        );
        
        // check routing
        $this->assertController('event');
        $this->assertAction('edit');
        
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate(
                        'msg_event_updated'
                    ),
                    $fm->getCurrentMessages()
        ));
        
        // check the redirecting
        $this->assertRedirectTo($this->url(
            array(
                'action' => 'index',
                'containerId'    => $this->category->id),
            'event')
        );
        
        //get ContainerRow from database
        $this->em->clear();
        $eventDBs = $this->em
                ->getRepository('ZC\Entity\Event')
                ->findAll();
        $eventDB = $eventDBs[0];
        
        $this->assertEquals(new \dateTime('2010-01-01'),$eventDB->date);
        
        $this->assertEquals(
            $itemGroupRow2->$propertyItem,
            $eventDB->$propertyItemGroup->$propertyItem
        );
    } 
    
    public function testCreateEventWithOneItemGroupPostSentSuccessful()
    {
        $propertyItem = Events_Model_ItemGroupRows::getPropertyName(
            $this->itemGroup->name,
            $this->item->name
        );
        
        $propertyItemGroup = Events_Model_ItemGroupRows::getPropertyName(
                $this->category->name,
                $this->itemGroup->name
        );
        
        $valuesItemGroupRowOnItemProperties = array(
            $propertyItem => 'value1'
        );
        $itemGroupRow1 = TestHelpersDoctrine::createItemGroupRowForItemGroup(
                $this->em,
                $this->itemGroup,
                $valuesItemGroupRowOnItemProperties,
                $this->member
        );
        
        $formKey = Backend_Model_ItemGroups::getFormItemName(
            $propertyItemGroup,
            $this->itemGroup->associationType
        );
        
        $input = array(
            'categoryId'        => $this->category->id,
            'date'              => '01-01-2010',
            $formKey            => $itemGroupRow1->id,
            'submit_insert'     => $this->vr->view->translate('action_save')
        );
        
        $this->request->setMethod('POST');
        $this->request->setPost($input);
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action' => 'create',
                'containerId'    => $this->category->id,
            ),
            'event')
        );
        
        // check routing
        $this->assertController('event');
        $this->assertAction('create');
        
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate(
                        'msg_event_created'
                    ),
                    $fm->getCurrentMessages()
        ));
        
        // check the redirecting
        $this->assertRedirectTo($this->url(
            array(
                'action' => 'index',
                'containerId'    => $this->category->id),
            'event')
        );
        
        //get ContainerRow from database
        $this->em->clear();
        $eventDBs = $this->em
                ->getRepository('ZC\Entity\Event')
                ->findAll();
        $eventDB = $eventDBs[0];
        
        $this->assertEquals(new \dateTime('2010-01-01'),$eventDB->date);
        
        $this->assertEquals(
            $itemGroupRow1->$propertyItem,
            $eventDB->$propertyItemGroup->$propertyItem
        );
    } 
}
