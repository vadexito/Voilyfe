<?php

/**
 * @group Controllers
 * @group Backend
 */

class CategoryControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    static protected $_categoryName;
    
    static protected $_itemNames;
    
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
        
        $this->_model = new Backend_Model_Categories($this->em);
    }
    
    public function testCategoryCreatePostSent()
    {
        //create itemIds
        $string = TestHelpersDoctrine::createItemString($this->em);
        $oneToMany = TestHelpersDoctrine::createItemOneToMany($this->em);
        $manyToOne = TestHelpersDoctrine::createItemManyToOne($this->em);
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $name = 'test';
        $name = TestHelpersDoctrine::addTestFilePrefix($name);
        $input = array(
            'name'          => $name,
            'itemIds'       => array(1,2,3),
            'submit_insert' => $this->vr->view->translate('action_create')
        );
        
        $this->request->setPost($input);
        
        // execute create category
         $this->dispatch($this->url(array('controller' => 'category','action'=> 'create'),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('category created successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertController('category');
        $this->assertAction('create');
        
        //check file
        $path = $this->_model->getContainerForRowsPath(
            $name
        );
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
        
        $entityName = $this->_model->getRowContainerEntityName($name);
        $this->assertNotNull($this->em->getRepository($entityName));
        $event = new $entityName;
        
        $propertyString = Events_Model_Events::getPropertyName(
                $name,
                $string->name
        );
        
        $propertyOneToMany = Events_Model_Events::getPropertyName(
                $name,
                $oneToMany->name
        );
        
        $propertyManyToOne = Events_Model_Events::getPropertyName(
                $name,
                $manyToOne->name
        );
        
        
        $this->assertInstanceOf($entityName,$event);
        $this->assertObjectHasAttribute('id',$event);
        $this->assertObjectHasAttribute('member',$event);
        $this->assertObjectHasAttribute($propertyString,$event);
        $this->assertObjectHasAttribute($propertyOneToMany,$event);
        $this->assertObjectHasAttribute($propertyManyToOne,$event);
        
    }
    
   public function testCategoryEditPostSent()
    {
        //create category
        $category = $this->initCategoryTest();
        $categoryId = $category->id;
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'name'          => $category->name,
            'itemIds'       => array(1),
            'submit_update' => $this->vr->view->translate('action_save')
        );
        
        $this->request->setPost($input);
        
        // execute create category
       $this->dispatch($this->url(
            array(
                'controller' => 'category',
                'action' => 'edit',
                'entityId' => $category->id,
            ),
            'backend'
        ));
        $this->assertController('category');
        $this->assertAction('edit');
        $this->assertRedirect();
       $this->assertRedirectTo($this->url(array(
            'controller'=> 'category',
            'action' => 'index'           
            ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('category updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check category row
        $this->em->clear();
        $categoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($categoryId);
        $this->assertNotNull($categoryDB);
        $this->assertEquals($category->name,$categoryDB->name);
        $this->assertEquals($input['itemIds'][0],$categoryDB->items[0]->id);
        
        
        //check file
        $path = $this->_model->getContainerForRowsPath(
            $category->name
        );
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
    }
    
    public function testCategoryDeletePostSent()
    {
        //create category
        $category = $this->initCategoryTest();
        
        $categoryId = $category->id;
        $path = $this->_model->getContainerForRowsPath(
            $category->name
        );
        
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'submit_delete' => $this->vr->view->translate('action_delete')
        );
        
        $this->request->setPost($input);
        
        // execute create category
         $this->dispatch($this->url(
            array(
                'controller'        => 'category',
                'action'            =>'delete',
                'entityId'          => $category->id),
            'backend'
        ));
        $this->assertController('category');
        $this->assertAction('delete');
        $this->assertRedirect();
       $this->assertRedirectTo($this->url(array(
            'controller'=> 'category',
            'action' => 'index'
            ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Category deleted successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check category row
        $categoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($categoryId);
        $this->assertNull($categoryDB);
        
        //check entity container file removed
        $this->assertFileNotExists($path);
    }
    
    public function testCategoryDeleteNotPostSent()
    { 
        $category = $this->initCategoryTest();
        $categoryId = $category->id;
        
        $this->dispatch($this->url(
            array(
                'controller' => 'category',
                'action'=>'delete',
                'entityId' => $category->id
            ),
            'backend'
        ));
        
        $this->assertController('category');
        $this->assertAction('delete');
        $this->assertNotRedirect();
        
        $this->em->clear();
        $categoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($categoryId);
        $this->assertEquals($categoryId,$categoryDB->id);
    }
   
    
}
