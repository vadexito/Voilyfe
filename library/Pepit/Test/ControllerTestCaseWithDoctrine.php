<?php

abstract class Pepit_Test_ControllerTestCaseWithDoctrine
                                    extends Zend_Test_PHPUnit_ControllerTestCase
{
    protected $application;
    protected $vr;
    
    protected $em;

    protected $category;
    protected $categoryEntityName;
    protected $_unlinkTeardown = array();
    
    static protected $_unlinkTearDownClass = array();
    static protected $_em ;
    
    const TEST_FILE_PREFIX = 'unittest';

    
    static public function setUpBeforeClass()
    {
        
        parent::setUpBeforeClass();
        
        //init Zend Framework
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $application->bootstrap();
        $bootstrap = $application->getBootstrap();
        $bootstrap  ->getResource('FrontController')
                    ->setParam('bootstrap', $bootstrap);
        
        self::$_em =Zend_Registry::get('entitymanager');
        
        //clean for filetests (failing tests)
        $dirs = array(
            Backend_Model_Categories::getContainerForRowsPath(),
            Backend_Model_Items::getFormElementPath(),
            Backend_Model_ItemGroups::getContainerForRowsPath(),
        );
        TestHelpersDoctrine::unlinkTestFiles($dirs);
        
        //inti doctrine
        TestHelpersDoctrine::initDoctrineSchema(self::$_em);
    }
    
    static public function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        
        foreach (self::$_unlinkTearDownClass as $file)
        {
            if (file_exists($file))
            {
                unlink($file);
            }
        }
        
        if( self::$_em)
        {
            self::$_em->getConnection()->close();
        }
        
        //clean for filetests (failing tests)
        $dirs = array(
            Backend_Model_Categories::getContainerForRowsPath(),
            Backend_Model_Items::getFormElementPath(),
            Backend_Model_ItemGroups::getContainerForRowsPath(),
        );
        TestHelpersDoctrine::unlinkTestFiles($dirs);
    }
    
    


    protected function setUp()
    {
        //boostrap ZF and get view renderer
        $this->bootstrap = array($this,'appBootstrap');
        
        //WARNING : always put setup right after bootstrap !
        parent::setUp();
        
        //get Doctrine init
        $this->em = Zend_Registry::get('entitymanager');
        TestHelpersDoctrine::initDoctrineSchema($this->em);
        
        $this->vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        
    }
    
    public function tearDown()
    {
        foreach ($this->_unlinkTeardown as $fileName)
        {
            if (file_exists($fileName))
            {
                unlink($fileName);
            }
        }
        
        $this->resetRequest();
        $this->resetResponse();
        $this->request->setPost(array());
        
        TestHelpersDoctrine::dropDoctrineSchema($this->em);
        
        parent::tearDown();
    }
    
    public function reinitDoctrine()
    {
        // define tool cli for doctrine and execute create entity
        $tool = new Doctrine\ORM\Tools\SchemaTool($this->em);
        
        $tool->dropSchema($this->em->getMetadataFactory()->getAllMetadata());
        
        $doctrineConfig = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV);        
        $doctrineConfig = $doctrineConfig->resources->doctrine->toArray();

        
        // create the Doctrine configuration
        $config = new \Doctrine\ORM\Configuration();

        // setting the cache
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        
        //metadata driver
        $configData = $doctrineConfig['dbal']['config']['parameters'];
        $chaindriver = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        
        $mainDriver = new Doctrine\ORM\Mapping\Driver\StaticPHPDriver(
            $configData['metadatadriver']['path']
        );        
        $chaindriver->addDriver($mainDriver,'ZC\Entity');
        
        $config->setMetadataDriverImpl($chaindriver);
        
        //proxies      
        $config->setProxyDir($configData['proxies']['dir']['path']);
        $config->setAutoGenerateProxyClasses((
                (APPLICATION_ENV == 'development') ||
                (APPLICATION_ENV == 'testing')
        ));
        $config->setProxyNamespace($configData['proxies']['namespace']);
        
        // create the entity manager and use the connection
        // settings we defined in our application.ini
        $connectionSettings = $doctrineConfig['dbal']['connection']['parameters'];
        $conn = array( 
            'driver'    => $connectionSettings['driv'],
            'user'      => $connectionSettings['user'],
            'password'  => $connectionSettings['pass'],
            'dbname'    => $connectionSettings['dbname'],
            'host'      => $connectionSettings['host'],
            'charset'   => 'utf8',
            'driverOptions' => array(1002 =>'SET NAMES utf8')
        );
        
        $newEm = \Doctrine\ORM\EntityManager::create($conn, $config);
        $this->em = $newEm;
        
        //reinitialize static property
        $tool = new Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->createSchema($newEm->getMetadataFactory()->getAllMetadata());
        
        return $newEm;  
    }
    
    public function initCategoryTest()
    {
        //create a line in the category table and item tables
        $category = TestHelpersDoctrine::createCategoryTest($this->em);
        $items = $category->items;
        $itemNames = array(
            'string'    => $items[0]->name,
            'oneToMany' => $items[1]->name,
            'manyToOne' => $items[2]->name,
        );
        
        //initialize properties
        $this->categoryEntityName = 
                Backend_Model_Categories::getRowContainerEntityName(
                        $category->name
        );
         
        //initialize files to be deleted after testing
        $this->_unlinkTeardown[] = 
                Backend_Model_Categories::getContainerForRowsPath($category->name);
        $this->_unlinkTeardown[] = 
                Backend_Model_Items::getFormElementPath($itemNames['string']);
        $this->_unlinkTeardown[] = 
                Backend_Model_Items::getFormElementPath($itemNames['oneToMany']);
        $this->_unlinkTeardown[] = 
                Backend_Model_Items::getFormElementPath($itemNames['manyToOne']);
                
        $this->category = $category;
        return $this->category;
    }
    
    
    public function appBootstrap()
    {
        $this->application = new Zend_Application(
            APPLICATION_ENV, 
            APPLICATION_PATH.'/configs/application.ini'
        );
        
        $this->application->bootstrap();
        
        $bootstrap = $this->application->getBootstrap();

        $bootstrap  ->getResource('FrontController')
                    ->setParam('bootstrap', $bootstrap);
    }
    
    
    /**
     * Simulate login of member
     */
    public function loginUser($user,$password)
    {
        // Prepare data for login
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'userName'          => $user,
            'userPassword'      => $password,
            'submit_login'      => $this->vr->view->translate('action_login')
        ));
        
        // execute login
        $this->dispatch($this->url(array(
            'action' => 'login'
        ),'access'));

        // empty variables after login
        $this->resetRequest();
        $this->resetResponse();
        $this->request->setPost(array());
    }
    
    /**
     * User logout
     */
    public function logoutUser()
    {
        // execute logout
        $this->dispatch($this->url(array(
            'action' => 'logout'
        ),'access'));

        // empty variables after logout
        $this->resetRequest();
        $this->resetResponse();
        $this->request->setPost(array());
    }
    
    public function assertSameItemPropertyForEvent(
                                    $associationType,$propertyTarget,$property)
    {
        if ($associationType === '0')
        {
            $this->assertEquals($propertyTarget,$property);
        }
        elseif ($associationType === \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY ||
                $associationType === \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY )
        {
            $this->assertEquals(
                $propertyTarget->count(),
                $property->count()
            );
            foreach ($propertyTarget as $key => $row)
            {
                $this->assertEquals($row->value,$property[$key]->value);
            }
        }
        elseif ($associationType === \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE ||
                $associationType === \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY )
        {
            $this->assertEquals($propertyTarget->value,$property->value);
        }
    }
}