<?php



abstract class Pepit_Test_DoctrineTestCase extends PHPUnit_Framework_TestCase
{
    
    protected $em;
    
    protected $_fileToUnlink = array();
    
    const TEST_FILE_PREFIX = 'MylifePHPUnitTestDoctrineTestCase';

    public function setUp()
    {      
        // bootstrap applicaion
        $application = new Zend_Application(APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        
        //bootstrap application
        $application->bootstrap();
        
        //initialize entity manager
        $this->em = Zend_Registry::get('entitymanager');
        
        //initialize doctrine
        TestHelpersDoctrine::initDoctrineSchema($this->em);
        
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        
        //drop existing database
        TestHelpersDoctrine::dropDoctrineSchema($this->em);
        
        //erase file added
        foreach($this->_fileToUnlink as $file)
        {
            unlink($file);
        }        
    }
    
    
    
}