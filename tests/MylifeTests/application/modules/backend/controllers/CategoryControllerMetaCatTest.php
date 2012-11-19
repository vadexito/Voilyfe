<?php

/**
 * @group Controllers
 * @group Backend
 */

class MetacategoryControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    protected $_fixture = array(
        'items'=>array(),
        'categories'=>array(),
    );
    
    static protected $_filesFixture = array(
        'item'      => array(),
        'category'  => array()
    );
    
    static protected $_categoryName;
    
    static protected $_itemNames;
    
    protected $_model;

    /**
     * create the files for one item and two categories
     * the file stay the same for all the tests
     *  
     */
    static public function setUpBeforeClass()
    {  
        parent::setUpBeforeClass();
        
        TestHelpersDoctrine::initBaseFormItems(self::$_em);
        
        $item = TestHelpersDoctrine::createItemString(
                self::$_em,
                null,
                true
        );
        self::$_filesFixture['item']['name'] = array($item->name);
        
        $categories = array(
            TestHelpersDoctrine::createCategoryWithItems(
                self::$_em,
                array($item),
                TestHelpersDoctrine::addTestFilePrefix('cat1')
            ),
            TestHelpersDoctrine::createCategoryWithItems(
                self::$_em,
                array($item),
                TestHelpersDoctrine::addTestFilePrefix('cat2')
            )
        );
        foreach ($categories as $category)
        {
            self::$_filesFixture['category']['name'][] = $category->name;
        }
    }

    /**
     * setting up the environnemeent for all the tests
     * 
     *  
     */
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
        
        //recreate the filefixture in the database
        TestHelpersDoctrine::initBaseFormItems(self::$_em);
        
        $item = TestHelpersDoctrine::createItemString(
                $this->em,
                self::$_filesFixture['item']['name'][0]
        );
       
        $this->_fixture['item'] = array($item);
        $categoryNames = self::$_filesFixture['category']['name'];
        foreach($categoryNames as $name)
        {
            $this->_fixture['categories'][] = 
                TestHelpersDoctrine::createCategoryWithItems(
                    $this->em,
                    array($item),
                    $name,
                    false
            );
        }
        
        //create admin access
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
    
    public function testMetacategoryCreatePostSent()
    {
        //create itemIds
        $metaCategoryIds = array();
        foreach ($this->_fixture['categories'] as $metaCategory)
        {
            $metaCategoryIds[] = $metaCategory->id;
        }
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $name = 'test';
        $name = TestHelpersDoctrine::addTestFilePrefix($name);
        $input = array(
            'name'                      => $name,
            'categoryIds'               => $metaCategoryIds,
            'submit_insert'             => $this->vr->view->translate('action_create')
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
        
        $metaCategoryDBs = $this ->em
                            ->getRepository('ZC\Entity\Category')
                            ->findAll();
        $this->assertEquals(3,count($metaCategoryDBs));
        $metaCategoryDB = $this ->em
                            ->getRepository('ZC\Entity\Category')
                            ->findOneByName($name);
        
        $this->assertNotNull($metaCategoryDB);
        $this->assertEquals($input['name'],$metaCategoryDB->name);
        
        $this->assertEquals(
            count($input['categoryIds']),
            $metaCategoryDB->categories->count()
        );
        
        foreach ($metaCategory->categories as $key => $category)
        {
            $this->assertEquals($input['categoryIds'][$key],$category->id);
        }
    }
   
   
   public function testCategoryEditPostSent()
    {
        //create category
        $metaCategory = $this->initCategoryTest();
        $metaCategoryId = $metaCategory->id;
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'name'          => $metaCategory->name,
            'itemIds'       => array(1),
            'submit_update' => $this->vr->view->translate('action_save')
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'category',
                'action' => 'edit',
                'entityId' => $metaCategory->id,
            ),
            'backend'
        ));
        $this->assertController('category');
        $this->assertAction('edit');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller'=> 'category',
            'action'=> 'index',
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
        $metaCategoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($metaCategoryId);
        $this->assertNotNull($metaCategoryDB);
        $this->assertEquals($metaCategory->name,$metaCategoryDB->name);
        $this->assertEquals($input['itemIds'][0],$metaCategoryDB->items[0]->id);
        
        
        //check file
        $path = $this->_model->getContainerForRowsPath(
            $metaCategory->name
        );
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
    }
    
    public function testCategoryDeletePostSent()
    {
        //create category
        $metaCategory = $this->initCategoryTest();
        
        $metaCategoryId = $metaCategory->id;
        $path = $this->_model->getContainerForRowsPath(
            $metaCategory->name
        );
        
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'submit_delete' => $this->vr->view->translate('action_delete')
        );
        
        $this->request->setPost($input);
        
        // execute delete category
        $this->dispatch($this->url(
            array('controller' => 'category','action'=>'delete','entityId' => $metaCategory->id),
            'backend'
        ));
        $this->assertController('category');
        $this->assertAction('delete');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller'=> 'category',
            'action'=>'index',
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
        $metaCategoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($metaCategoryId);
        $this->assertNull($metaCategoryDB);
        
        //check entity container file removed
        $this->assertFileNotExists($path);
    }
    
    public function testCategoryDeleteNotPostSent()
    { 
        $metaCategory = $this->initCategoryTest();
        $metaCategoryId = $metaCategory->id;
        
        // execute delete category
        $this->dispatch($this->url(
            array('controller' => 'category','action'=>'delete','entityId' => $metaCategory->id),
            'backend'
        ));
        
        $this->assertController('category');
        $this->assertAction('delete');
        $this->assertNotRedirect();
        
        $this->em->clear();
        $metaCategoryDB = $this->em ->getRepository('ZC\Entity\Category')
                                ->find($metaCategoryId);
        $this->assertEquals($metaCategoryId,$metaCategoryDB->id);
    }
   
    
}
