<?php

require_once APPLICATION_PATH.'/modules/events/controllers/helpers/LastVisited.php';

/**
 * @group Controllers
 * @group Events
 * 
 */
class EventControllerLinkingTest
    extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    protected $container;
    
    static protected $containerRowType;
    static protected $containerType;
    static protected $containerRowEntity;
    static protected $containerRowModel;
    static protected $containerModel;
    
    static protected $_containerName;
    static protected $_itemNames;
    
    
    
    static public function setUpBeforeClass()
    {
        self::$containerRowType = 'event';
        self::$containerType = 'category';
        self::$containerRowEntity = 'ZC\Entity\Event';
        self::$containerRowModel = 'Events_Model_Events';
        self::$containerModel = 'Backend_Model_Categories';
        
        parent::setUpBeforeClass();
        
        //create the files for category and three items in test
        $container = call_user_func_array(
               array(
                   'TestHelpersDoctrine',
                   'create'.ucfirst(self::$containerType).'Test'
                ),
               array(self::$_em)
        );
        
        self::$_containerName = $container->name;
        
        self::$_itemNames = array(
            'string'    => $container->items[0]->name,
            'oneToMany' => $container->items[1]->name,
            'manyToOne' => $container->items[2]->name,
        );
        self::$_unlinkTearDownClass[] = 
            call_user_func_array(
               array(
                   self::$containerModel,
                   'getContainerForRowsPath'
                ),
               array($container->name)
        );
        foreach (self::$_itemNames as $name)
        {
            self::$_unlinkTearDownClass[] = 
            Backend_Model_Items::getFormElementPath($name);
        }   
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->container = call_user_func_array(
            array(
                'TestHelpersDoctrine',
                'create'.ucfirst(self::$containerType).'Test'
                ),
                array(
                    $this->em, 
                    self::$_containerName,
                    self::$_itemNames,
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
        
        //mock server
        $_SERVER['HTTP_HOST'] = 'mylife';
        $_SERVER['HTTPS'] = 'off';
        
        
    }
    
    public function testLinkingThroughAddButton()
    {
        $lastVisited = new Events_Controller_Action_Helper_LastVisited();
        
        $urlEvent = $this->url(
            array(
                'action' => 'create',
                'containerId'        => 1,
            ),
            'event'
        );
        $urlEventFull = 'http://'.$_SERVER['HTTP_HOST'].$urlEvent;
        $urlItemRowCreate = $this->url(
            array(
                'controller'        => 'itemrow',
                'action'                    => 'create',
                'containerId'               => 3,
                'askComeBack'               => 'true',
            ),
            'event'
        );
        
        $this->dispatch($urlEvent);
        
        //dispatch to create item row with asking to remember previous url
        $testUrlLastVisited = $urlEventFull;
        $_SERVER['HTTP_REFERER'] = $testUrlLastVisited;
        $_SERVER['REQUEST_URI'] = $urlItemRowCreate;
        $this->dispatch($urlItemRowCreate);
        $this->assertController('itemrow');
        $this->assertAction('create');
        $this->assertEquals($testUrlLastVisited,$lastVisited->getLastVisited());
        
         // prepare item row to be created successfully
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'value'                  => 'value',
            'itemId'                 => 3,
            'submit_insert'          => $this->vr->view->translate('action_save')
        ));
        
        $urlItemRowCreate = $this->url(
            array(
                'controller' => 'itemrow',
                'action' => 'create',
                'containerId'               => 3,
                'askComeBack'          => 'false',
            ),
            'event'
        );
        
        
        $_SERVER['HTTP_REFERER'] = 'http://'.$_SERVER['HTTP_HOST'].$urlItemRowCreate;
        $_SERVER['REQUEST_URI'] = $urlItemRowCreate;
        
        // go the url
        $this->dispatch($this->url(
            array(
                'controller' => 'itemrow',
                'action' => 'create',
                'containerId'               => 3,
                'askComeBack'          => 'false',
            ),
            'event'
        ));
        
        //redirect to url instead of home
        $this->assertRedirect();
        $this->assertRedirectTo($urlEventFull);
    }
    
    public function testLinkingThroughAddButtonStayOnTheSamePage()
    {
        $lastVisited = new Events_Controller_Action_Helper_LastVisited();
        
        $urlEvent = $this->url(
            array(
                'action' => 'create',
                'containerId'        => 1,
            ),
            'event'
        );
        $urlEventFull = 'http://'.$_SERVER['HTTP_HOST'].$urlEvent;
        $urlItemRowCreate = $this->url(
            array(
                'controller'                => 'itemrow',
                'action'                    => 'create',
                'containerId'               => 1,
                'askComeBack'               => 'true',
            ),
            'event'
        );
        
        $this->dispatch($this->url(array(),'home'));
        $this->dispatch($urlEvent);
        
        $this->assertController('event');
        $this->assertAction('create');
        
        
        //dispatch to create item row with asking to remember previous url
        $_SERVER['HTTP_REFERER'] = $urlEventFull;
        $_SERVER['REQUEST_URI'] = $urlItemRowCreate;
       
        $this->dispatch($urlItemRowCreate);
        $this->assertController('itemrow');
        $this->assertAction('create');
        $this->assertEquals($urlEventFull,$lastVisited->getLastVisited());
        
        
        //staying on the same page doesn't change to redirection
        $_SERVER['HTTP_REFERER'] =  $urlEventFull;
        $_SERVER['REQUEST_URI'] = $urlItemRowCreate;
        $this->dispatch($urlItemRowCreate);
        $this->assertController('itemrow');
        $this->assertAction('create');
        $this->assertEquals($urlEventFull,$lastVisited->getLastVisited());
        
        
    }
    
    public function testResetForLinkingThroughAddButton()
    {
        
        $lastVisited = new Events_Controller_Action_Helper_LastVisited();
        $lastVisited->addLastVisited('test');
        $this->assertEquals('test',$lastVisited->getLastVisited());
        
        $urlEvent = $this->url(
            array(
                'action' => 'create',
                'containerId'        => 1,
            ),
            'event'
        );
        
        $this->dispatch($this->url(array(),'home'));
        $this->dispatch($urlEvent);
        
        $this->assertController('event');
        $this->assertAction('create');
        
        
        //on case of interrupting chain, Lastvisited is reset
        $_SERVER['HTTP_REFERER'] =  'http://mylife/home';
        $_SERVER['REQUEST_URI'] = $urlEvent;
        $this->dispatch($urlEvent);
        $this->assertNull($lastVisited->getLastVisited());
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
                    self::$containerRowModel,
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
        
        $container = self::$containerType;
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
