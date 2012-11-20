<?php



abstract class Pepit_Test_ZendTestCase extends PHPUnit_Framework_TestCase
{
    
    protected $em;
    
    const TEST_FILE_PREFIX = 'MylifePHPUnitTestZendTestCase';
    
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
        
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }
    
    public function getCategoryMockWithItems()
    {
        $generalizedItemMock1 = $this->getMock('ZC\Entity\GeneralizedItem');
        $generalizedItemMock2 = $this->getMock('ZC\Entity\GeneralizedItem');
        $generalizedItemMock3 = $this->getMock('ZC\Entity\GeneralizedItem');
        
        $categoryMock = new stdClass();
        $categoryMock->items = array(
            $generalizedItemMock1,
            $generalizedItemMock2,
            $generalizedItemMock3
        );
        $categoryMock->name = 'name';
        
        return $categoryMock;
    }
    
    public function getEntityManagerMock()
    {
        $emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->getMock();
        
        return $emMock;
    }
}