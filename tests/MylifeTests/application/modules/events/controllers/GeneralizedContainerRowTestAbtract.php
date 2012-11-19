<?php


abstract class GeneralizedContainerRowTestAbtract extends Pepit_Test_ControllerTestCaseWithDoctrine

{
    
    protected $member;
    
    protected $memberPass;
    
    protected $container;
    
    static protected $containerRowType;
    static protected $containerType;
    static protected $containerRowEntity;
    static protected $containerRowModel;
    static protected $containerModel;
    static protected $_container;
    static protected $_containerName;
    static protected $_itemNames;
   
    
    
    

    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        //create the files for category aznd three items in test
        $container = call_user_func_array(
               array(
                   'TestHelpersDoctrine',
                   'create'.ucfirst(static::$containerType).'Test'
                ),
               array(static::$_em)
        );
        self::$_container = $container;
        
        static::$_containerName = $container->name;
        
        static::$_itemNames = array(
            'string'    => $container->items[0]->name,
            'oneToMany' => $container->items[1]->name,
            'manyToOne' => $container->items[2]->name,
        );
        
        
        
        static::$_unlinkTearDownClass[] = 
            call_user_func_array(
               array(
                   static::$containerModel,
                   'getContainerForRowsPath'
                ),
               array($container->name)
        );
        foreach (static::$_itemNames as $name)
        {
            static::$_unlinkTearDownClass[] = 
            Backend_Model_Items::getFormElementPath($name);
        }   
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->container = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(static::$containerType).'Test'
                ),
                array(
                    $this->em, 
                    static::$_containerName,
                    static::$_itemNames,
                    false
        ));
        
        
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
            $this->em, 
            'userName',
            $this->memberPass,true,true
        );
        
        //login
        $this->loginUser($this->member->userName,$this->memberPass);
        
    }

public function testPreDispathCheckingEditForMemberNotAuthor()
    {
        $this->dispatch($this->url(
            array(
                'controller'        => strtolower(static::$containerRowType),
                'action'            => 'edit',
                'containerId'       => $this->container->id,
                'containerRowId'    => 6
            ),
            'event'   
        ));
        
        $this->assertController(strtolower(static::$containerRowType));
        $this->assertAction('edit');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'action' => 'login'
        ),'access'));
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());       
        
    }
    
    public function testPreDispathCheckingEditForMemberIsAuthor()
    {
        $modelName  = static::$containerRowModel;
        $generalizedContainerRow = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(static::$containerRowType)
                    .'For'
                    .ucfirst(static::$containerType) .'Test'
                ),
                array(
                    $this->em,
                    new $modelName($this->em),
                    $this->container,
                    $this->member
        ));
        

         // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'edit',
                'containerId'       => $this->container->id,
                'containerRowId'    => $generalizedContainerRow->id
            ),
            'event'   
        ));

        $this->assertNotRedirect();
        $this->assertInstanceOf('Zend_Form',$this->vr->view->form);
    }       
            
            
    public function testCreateNewContainerRowSuccessful()
    {
        // prepare data to be sent
        $this->request->setMethod('POST');
        
        $metadataItems = $this->_initMetadaItems();
        
        $input = array(
            static::$containerType.'Id'   => $this->container->id,
            $metadataItems['string']['formName']        => 'testString',
            $metadataItems['oneToMany']['formName']     => 'new value1,new value2',
            $metadataItems['manyToOne']['formName']     => 2,
            'submit_insert'                             => $this->vr->view->translate('action_save')
        );
        if (static::$containerType === 'category')
        {
            $input['date'] = '01-01-2012';
        }
        
        $this->request->setPost($input);
       
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'create',
                'containerId'    => $this->container->id,
            ),
            'event'   
        ));
        
        
        // check routing
        $this->assertController(strtolower(static::$containerRowType));
        $this->assertAction('create');
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('msg_'.strtolower(static::$containerRowType).'_created'),
                    $fm->getCurrentMessages()
        ));
        
        // check the redirecting
        $this->assertRedirectTo($this->url(
                array(
                    'controller' => strtolower(static::$containerRowType),
                    'action' => 'index',
                    'containerId'=>  $this->container->id),
               'event'
        ));

        //get ContainerR
        //ow from database
        $generalizedContainerRowDBs = $this->em
                ->getRepository('ZC\Entity\\'.ucfirst(static::$containerRowType))
                ->findAll();
        
        $generalizedContainerRowDB = $generalizedContainerRowDBs[0];
        $this->assertContainerRowDBEqualsInput($input, $metadataItems, $generalizedContainerRowDB);
        
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$generalizedContainerRowDB->creationDate->getTimeStamp()),10);
        if (property_exists($generalizedContainerRowDB, 'date'))
        {
             $this->assertEquals(new \dateTime('2012-01-01'),$generalizedContainerRowDB->date);
        }
    }
    
    public function testCreateNewContainerRowNoPostSent()
    {
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'create',
                'containerId'    => $this->container->id,
            ),
            'event'   
        ));
        
        // check routing
        $this->assertController(strtolower(static::$containerRowType));
        $this->assertAction('create');
        
        // check the redirecting
        $this->assertNotRedirect();
        
        //check the form 
        $this->assertInstanceOf('Zend_Form',$this->vr->view->form);
    }
    
    public function testDeleteContainerRowPostSentSuccessful()
    {
        $modelName = static::$containerRowModel;
        $generalizedContainerRow = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(static::$containerRowType)
                    .'For'
                    .ucfirst(static::$containerType) .'Test'
                ),
                array(
                    $this->em,
                    new $modelName($this->em),
                    $this->container,
                    $this->member
        ));
        
        $this->request->setMethod('POST');
        $this->request->setPost( array(
            'submit_delete'  => $this->vr->view->translate('action_delete')
        ));
        
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'delete',
                'containerId'    => $this->container->id,
                'containerRowId'       => $generalizedContainerRow->id,
            ),
            'event')
        );
       
        
        //check ContainerRow deleted
        $generalizedContainerRows = $this->em
                ->getRepository(static::$containerRowEntity)
                ->findAll();
        
        $this->assertEmpty($generalizedContainerRows);
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('msg_'.strtolower(static::$containerRowType).'_deleted'),
                    $fm->getCurrentMessages()
        ));
        
        //check redirect
        $this->assertRedirectTo($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'index',
                'containerId'    => $this->container->id,
            ),
            'event')
        );
    }  
    
    public function testDeleteContainerRowErrorNoContainerRowToDelete()
    {
        // go the url
        
        $this->dispatch($this->url(
            array(
                'controller'            => strtolower(static::$containerRowType),
                'action'                => 'delete',
                'containerId'           => $this->container->id,
                'containerRowId'        => 100,
            ),
            'event')
        );
        
        $this->assertRedirectTo($this->url(array(
            'action' => 'login'
        ),'access'));
    }  
    
    public function testEditContainerRowNoPostSent()
    {
        $model = static::$containerRowModel;
        $generalizedContainerRow = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(static::$containerRowType)
                    .'For'
                    .ucfirst(static::$containerType) .'Test'
                ),
                array(
                    $this->em,
                    new $model($this->em),
                    $this->container,
                    $this->member
        ));
        
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'edit',
                'containerId'    => $this->container->id,
                'containerRowId'       => $generalizedContainerRow->id,
            ),
            'event')
        );
        
        // check routing
        $this->assertController(strtolower(static::$containerRowType));
        $this->assertAction('edit');
        
         
        // check the redirecting
        $this->assertNotRedirect();
    }
    
    
    
    public function testEditContainerRowPostSentSuccessful()
    {
        $model = static::$containerRowModel;
        $generalizedContainerRow = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(static::$containerRowType)
                    .'For'
                    .ucfirst(static::$containerType) .'Test'
                ),
                array(
                    $this->em,
                    new $model($this->em),
                    $this->container,
                    $this->member
        ));
        
        $metadataItems = $this->_initMetadaItems();
        
        $input = array(
            static::$containerType.'Id'        => $this->container->id,
            'date'              => '01-01-2010',
            $metadataItems['string']['formName']            => 'testStringBis',
            $metadataItems['oneToMany']['formName']         => 'newvalue1,newvalue2,newvalue3',
            $metadataItems['manyToOne']['formName']         => 1,
            'submit_update'                                 => $this->vr->view->translate('action_save')
        );
        
        $this->request->setMethod('POST');
        $this->request->setPost($input);
        
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'edit',
                'containerId'    => $this->container->id,
                'containerRowId'       => $generalizedContainerRow->id,
            ),
            'event')
        );
        
        // check routing
        $this->assertController(strtolower(static::$containerRowType));
        $this->assertAction('edit');
        
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate(
                        'msg_'.strtolower(static::$containerRowType).'_updated'
                    ),
                    $fm->getCurrentMessages()
        ));
        
        // check the redirecting
        $this->assertRedirectTo($this->url(
            array(
                'controller' => strtolower(static::$containerRowType),
                'action' => 'index',
                'containerId'    => $this->container->id,
            ),
            'event')
        );
        
        //get ContainerRow from database
        $generalizedContainerRowDBs = $this->em
                ->getRepository(static::$containerRowEntity)
                ->findAll();
        $generalizedContainerRowDB = $generalizedContainerRowDBs[0];
        
        if (property_exists($generalizedContainerRowDB, 'date'))
        {
           $this->assertEquals(new \dateTime('2010-01-01'),$generalizedContainerRowDB->date);
        }
        $this->assertContainerRowDBEqualsInput($input, $metadataItems, $generalizedContainerRowDB);
    } 
    
    protected function _initMetadaItems()
    {
        $metadataItems = array();
        
        $metadataItems = $this->_defineSingleMetadata(
                $metadataItems,
                'string',
                $this->container->items[0]->name,
                $this->container->name,
                '0'
        );
       
        $metadataItems = $this->_defineSingleMetadata(
                $metadataItems,
                'oneToMany',
                $this->container->items[1]->name,
                $this->container->name,
                \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
        );
       
        $metadataItems = $this->_defineSingleMetadata(
                $metadataItems,
                'manyToOne',
                $this->container->items[2]->name,
                $this->container->name,
                \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
        );
        
        return $metadataItems;
    }
    
    protected function _defineSingleMetadata(
        array $metadataItems,$type,$itemName,$containerName,$associationType)
    {
        
        $metadataItems[$type] = array(
          'itemName'        => $itemName);
        $metadataItems[$type]['propertyName'] = 
            call_user_func_array(array(
                    static::$containerRowModel,
                    'getPropertyName'
                ), 
                array(
                    $containerName, 
                    $metadataItems[$type]['itemName']
        ));
        
        $metadataItems[$type]['formName'] = 
            Backend_Model_Items::getFormItemName(
                $metadataItems[$type]['propertyName'],
                $associationType
            );
        
        return $metadataItems;
    }


    protected function assertContainerRowDBEqualsInput($input,$metadataItems,$generalizedContainerRowDB)
    {
        
        $container = static::$containerType;
        //check all elements
        $this->assertEquals($this->container->id,$generalizedContainerRowDB->$container->id);
        $this->assertEquals($this->member->userName,$generalizedContainerRowDB->member->userName);
        
        
        $propertyNameString = $metadataItems['string']['propertyName'];
        $this->assertEquals(
                $input[$metadataItems['string']['formName']],
                $generalizedContainerRowDB->$propertyNameString
        );
        
        $options = explode(',',$input[$metadataItems['oneToMany']['formName']]);
        $propertyNameOneToMany = $metadataItems['oneToMany']['propertyName'];
        $oneToManyDB = $generalizedContainerRowDB->$propertyNameOneToMany;
        $this->assertEquals(count($options),$oneToManyDB->count());
        foreach($oneToManyDB as $option)
        {
            $this->assertTrue(in_array($option->value,$options));
        }
        
        $propertyNameManyToOne = $metadataItems['manyToOne']['propertyName'];;
        $this->assertEquals(
            $this->em->getRepository('ZC\Entity\ItemRow')->find(
                $input[$metadataItems['manyToOne']['formName']]
            ),
            $generalizedContainerRowDB->$propertyNameManyToOne
        );
        
        //check the date fields
        $this->assertInstanceOf('\DateTime',$generalizedContainerRowDB->creationDate);
        $this->assertInstanceOf('\DateTime',$generalizedContainerRowDB->modificationDate);
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$generalizedContainerRowDB->modificationDate->getTimeStamp()),10);
    }
    
    
}
