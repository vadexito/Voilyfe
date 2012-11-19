<?php

/**
 * @group Controllers
 * @group Backend
 */

class ItemGroupControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    protected $_model;
    
    //override setup in order to cancel any file creation
    static public function setUpBeforeClass()
    { 
    }

    public function setUp()
    {
        parent::setUp();
        
        //clean for filetests (failing tests)
        $dirs = array(
            Backend_Model_Categories::getContainerForRowsPath(),
            Backend_Model_Items::getFormElementPath(),
            Backend_Model_ItemGroups::getContainerForRowsPath(),
        );
        
        TestHelpersDoctrine::unlinkTestFiles($dirs);
        
        //create admin
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
            $this->em,
            'userName',
            $this->memberPass,true,true
        );
        $this->member->role = "admin";
        
        TestHelpersDoctrine::initBaseFormItems($this->em);
        
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $this->_model = new Backend_Model_ItemGroups($this->em);
    }
    
    public function testIndex()
    {
        $this->dispatch($this->url(array(
            'controller' => 'itemgroup'
        ),'backend'));
        
        $this->assertNotRedirect();
        
        $this->assertEquals('index.phtml',$this->vr->getViewScript());
    }
    
    /**
     *
     * @dataProvider providerInputItemGroupCreateSuccess
     */
    
    public function testItemGroupCreatePostSentSuccess($input)
    {
        //create itemIds
        $string = TestHelpersDoctrine::createItemString($this->em);
        $oneToMany = TestHelpersDoctrine::createItemOneToMany($this->em);
        $manyToOne = TestHelpersDoctrine::createItemManyToOne($this->em);
        
        // Prepare request
        $this->request->setMethod('POST');
        $time = new \DateTime();
        $name = 'test'.$time->getTimestamp();
        
        $formElementClass = $this->em
                        ->getRepository('ZC\Entity\FormElementClass')
                        ->findOneByName($input['formElementClass']);
        unset($input['formElementClass']);
        
        $input = array_merge($input,array(
            'name'                  => $name,
            'formElementClassId'    => $formElementClass->id,
            'submit_insert'         => $this->vr->view->translate('action_create'),
        ));
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(array(
            'controller' => 'itemgroup',
            'action' => 'create',
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item Group created successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertController('itemgroup');
        $this->assertAction('create');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller' => 'itemgroup',
            'action' => 'index',
        ),'backend'));
        
        //check file
        $path = $this->_model->getContainerForRowsPath($name);
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
        
        //check data base
        $this->em->clear();
        $itemgroupDB = $this->em->getRepository('ZC\Entity\ItemGroup')->find(4);
        
        $this->assertInstanceOf('ZC\Entity\ItemGroup',$itemgroupDB);
        
        $this->assertEquals(
            $input['formElementClassId'],
            $itemgroupDB->formElementClass->id
        );
        
        $this->assertEquals(
            $input['identifierItemId'],
            $itemgroupDB->identifierItem->id
        );
        
        $this->assertEquals(
            $input['formRequired'] === '1',
            $itemgroupDB->formRequired
        );
        
        unset($input['formElementClassId']);
        unset($input['identifierItemId']);
        unset($input['formRequired']);
        $itemIds = $input['itemIds'];
        unset($input['itemIds']);
        unset($input['submit_insert']);
        
        foreach ($input as $property => $value)
        {
            $this->assertEquals($value,$itemgroupDB->$property);
        }
        
        
        //test container
        $entityName = $this->_model->getRowContainerEntityName($name);
        $this->assertNotNull($this->em->getRepository($entityName));
        $itemgroupRow = new $entityName;
        
        $property = array();
        $property[1] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $string->name
        );
        
        $property[2] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $oneToMany->name
        );
        
        $property[3] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $manyToOne->name
        );
        
        
        $this->assertInstanceOf($entityName,$itemgroupRow);
        $this->assertObjectHasAttribute('id',$itemgroupRow);
        $this->assertObjectHasAttribute('member',$itemgroupRow);
        
        foreach ($itemIds as $itemId)
        {
            $this->assertObjectHasAttribute($property[$itemId],$itemgroupRow);
        }
    }
    
    public function providerInputItemGroupCreateSuccess()
    {
        return array(
            array(array(
                    'itemType'              => 2,
                    'formElementClass'      => 'Pepit_Form_Element_Select',
                    'formRequired'          => '1',
                    'associationType'       => '2', // MANY_TO_ONE
                    'identifierItemId'      => 1, 
                    'itemIds'               => array(1,2,3)
                    )
                ),
            array(array(
                    'itemType'              => 2,
                    'formElementClass'      => 'Pepit_Form_Element_Select',
                    'formRequired'          => '0',
                    'associationType'       => '2', // MANY_TO_ONE
                    'identifierItemId'      => 2,
                    'itemIds'               => array(1)
                    )
                ),
           
        );
    }
    
    public function testItemGroupCreatePostSentFailure()
    {
        // Prepare request
        $this->request->setMethod('POST');
        $time = new \DateTime();
        $name = 'test'.$time->getTimestamp();
        
        $input = array(
            'name'                  => $name,
            'itemType'              => 2,
            'formElementClassId'    => 2,
            'formRequired'          => '0',
            'associationType'       => '2', // MANY_TO_ONE
            'identifierItemId'      => 2,
            'itemIds'               => '',
            'submit_insert'         => $this->vr->view->translate('action_create'),
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(array(
            'controller' => 'itemgroup',
            'action' => 'create',
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item Group created successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertController('itemgroup');
        $this->assertAction('create');
        $this->assertNotRedirect();
        
        //check populating
        $form = $this->vr->view->form;
                
        unset($input['submit_insert']);
        $populated = $input;
        
        foreach ($populated as $key => $value)
        {
            $this->assertEquals($value,$form->getElement($key)->getValue());
        }
    }
    
    /**
     * @dataProvider providerInputItemGroupEditSuccess 
     * 
     * 
     */
    public function testItemGroupEditPostSentSuccess($input)
    {
        $itemgroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        $itemgroupId = $itemgroup->id;
        $name = $itemgroup->name;
        $string = $itemgroup->items[0];
        $oneToMany = $itemgroup->items[1];
        $manyToOne =$itemgroup->items[2];
        
        $formElementClass = $this->em
                        ->getRepository('ZC\Entity\FormElementClass')
                        ->findOneByName($input['formElementClass']);
        unset($input['formElementClass']);
        
        $input = array_merge($input,array(
            'name'                  => $name, //shouldn't be changed in practice
            'formElementClassId'    => $formElementClass->id,
            'submit_update'         => $this->vr->view->translate('action_save'),
        ));
        
        $this->request->setMethod('POST');
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'itemgroup',
                'action' => 'edit',
                'entityId' => $itemgroupId
                ),
            'backend'
        ));
        
        $this->assertController('itemgroup');
        $this->assertAction('edit');
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item Group updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller' => 'itemgroup'
        ),'backend'));
        
        //check file existence
        $path = $this->_model->getContainerForRowsPath($name);
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
        
        //check item group in data base
        $this->em->clear();
        $itemgroupDB = $this->em
                            ->getRepository('ZC\Entity\ItemGroup')
                            ->find($itemgroupId);
        
        $this->assertInstanceOf('ZC\Entity\ItemGroup',$itemgroupDB);
        
        $this->assertEquals(
            $input['formElementClassId'],
            $itemgroupDB->formElementClass->id
        );
        
        $this->assertEquals(
            $input['formRequired'] === '1',
            $itemgroupDB->formRequired
        );
       
       $this->assertEquals(
            $itemgroup->identifierItem->id, //is not changed
            $itemgroupDB->identifierItem->id
        );
        
        
        unset($input['formElementClassId']);
        unset($input['identifierItemId']);
        unset($input['formRequired']);
        $itemIds = $input['itemIds'];
        unset($input['itemIds']);
        unset($input['submit_update']);
        
        foreach ($input as $property => $value)
        {
            $this->assertEquals($value,$itemgroupDB->$property);
        }
        
        
        //test container
        $entityName = $this->_model->getRowContainerEntityName($name);
        $this->assertNotNull($this->em->getRepository($entityName));
        $itemgroupRow = new $entityName;
        
        $property = array();
        $property[1] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $string->name
        );
        
        $property[2] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $oneToMany->name
        );
        
        $property[3] = Events_Model_ItemGroupRows::getPropertyName(
                $name,
                $manyToOne->name
        );
        
        
        $this->assertInstanceOf($entityName,$itemgroupRow);
        $this->assertObjectHasAttribute('id',$itemgroupRow);
        $this->assertObjectHasAttribute('member',$itemgroupRow);
        
        foreach ($itemIds as $itemId)
        {
            $this->assertObjectHasAttribute($property[$itemId],$itemgroupRow);
        }
    }
    
    public function providerInputItemGroupEditSuccess()
    {
        return array(
            array(array(
                    'itemType'              => 2,
                    'formElementClass'      => 'Pepit_Form_Element_Select',
                    'formRequired'          => '1',
                    'associationType'       => '2', // MANY_TO_ONE
                    'identifierItemId'      => 1, 
                    'itemIds'               => array(1,2,3)
                    )
                ),
            array(array(
                    'itemType'              => 2,
                    'formElementClass'      => 'Pepit_Form_Element_Select',
                    'formRequired'          => '0',
                    'associationType'       => '2', // MANY_TO_ONE
                    'identifierItemId'      => 2,
                    'itemIds'               => array(1)
                    )
                ),
           
        );
    }
    
    public function testItemGroupEditPostSentNotValid()
    {
        $itemgroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        $itemgroupId = $itemgroup->id;
        
        // Prepare data
        $this->request->setMethod('POST');
        
        //name is changed only for testing (to avoid the initial require_once)
        $input = array(
            'name'                  => 'name',
            'itemType'              => 2,
            'formElementClassId'    => 2,
            'formRequired'          => '0',
            'associationType'       => '2', // MANY_TO_ONE
            'identifierItemId'      => 2,
            'itemIds'               => '',
            'submit_insert'         => $this->vr->view->translate('action_create'),
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller'    => 'itemgroup',
                'action'        => 'edit',
                'entityId'      => $itemgroupId),
            'backend'
        ));
        $this->assertController('itemgroup');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check populating
        $form = $this->vr->view->form;
                
        $singleProperties = array('name','itemType','formRequired',
            'associationType');
        foreach ($singleProperties as $property)
        {
            $this->assertEquals($input[$property],$form->getElement($property)->getValue());
        }
        $this->assertEquals($input['formElementClassId'],$form->getElement('formElementClassId')->getValue());
        $this->assertEquals($input['identifierItemId'],$form->getElement('identifierItemId')->getValue());
    }
    
    public function testItemGroupEditPostNotSent()
    {
        $itemgroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        $itemgroupId = $itemgroup->id;
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'itemgroup',
                'action' => 'edit',
                'entityId' => $itemgroupId),
            'backend'
        ));
        $this->assertController('itemgroup');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check populating
        $form = $this->vr->view->form;
                
        $singleProperties = array('name','itemType','formRequired',
            'associationType');
        foreach ($singleProperties as $property)
        {
            $this->assertEquals($itemgroup->$property,$form->getElement($property)->getValue());
        }
        $this->assertEquals($itemgroup->formElementClass->id,$form->getElement('formElementClassId')->getValue());
        $this->assertEquals($itemgroup->identifierItem->id,$form->getElement('identifierItemId')->getValue());
       
    }
    
    public function testItemGroupDeletePostSent()
    {
        $itemgroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        $itemgroupId = $itemgroup->id;
        $path = $this->_model->getContainerForRowsPath($itemgroup->name);
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'submit_delete' => $this->vr->view->translate('action_delete')
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'itemgroup',
                'action' => 'delete',
                'entityId' => $itemgroupId),
            'backend'
        ));
        $this->assertController('itemgroup');
        $this->assertAction('delete');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller' => 'itemgroup'
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item Group deleted successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check itemgroup row not existing
        $itemgroupDB = $this->em ->getRepository('ZC\Entity\ItemGroup')
                                ->find($itemgroupId);
        $this->assertNull($itemgroupDB);
        $generalizedItemDB = $this->em ->getRepository('ZC\Entity\GeneralizedItem')
                                ->find($itemgroupId);
        $this->assertNull($generalizedItemDB);
        
        //check form element file
        $this->assertFileNotExists($path);
    }
    
    public function testItemGroupDeleteNotPostSent()
    {
        $itemgroup = TestHelpersDoctrine::createItemGroupTest($this->em);
        $itemgroupId = $itemgroup->id;
        $path = $this->_model->getContainerForRowsPath($itemgroup->name);
        
         // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'itemgroup',
                'action' => 'delete',
                'entityId' => $itemgroupId,
                ),
            'backend'
        ));
        $this->assertController('itemgroup');
        $this->assertAction('delete');
        $this->assertNotRedirect();
        
        //check element is still in database
        $this->em->clear();
        $itemgroupDB = $this->em ->getRepository('ZC\Entity\ItemGroup')
                            ->find($itemgroupId);
        $this->assertEquals($itemgroupId,$itemgroupDB->id);
        
        //check form element file still exists
        $this->assertFileExists($path);
    }
}
