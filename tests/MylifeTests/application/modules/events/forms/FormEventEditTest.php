<?php

/**
 * @group Forms
 * @group Events
 * 
 */
class FormEventEditTest extends Pepit_Test_ZendTestCase
{
    /**
     * @var Zend_Form
     */
    protected $_form = null;
    
    public function setUp() 
    {
        parent::setUp();
        
        $categoryMock = $this->getCategoryMockWithItems();
        
        $emMock = $this->getEntityManagerMock();
        
        $repositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                ->disableOriginalConstructor()
                ->getMock();        
        $repositoryMock->expects($this->any())
             ->method('find')
             ->will($this->returnValue($categoryMock));
        
        
        $emMock->expects($this->any())
             ->method('getRepository')
             ->will($this->returnValue($repositoryMock));
        
        $modelMock = $this->getMockBuilder('Events_Model_Events')
                ->disableOriginalConstructor()
                ->getMock();
        $modelMock->expects($this->any())
             ->method('getEntityManager')
             ->will($this->returnValue($emMock));
        
        $formElement1 = new Zend_Form_Element_Text('test1');
        $formElement2 = new Zend_Form_Element_Text('test2');
        $formElement3 = new Zend_Form_Element_Text('test3');
        
        $modelMock->expects($this->any())
             ->method('getFormElement')
             ->will($this->onConsecutiveCalls(
                     $formElement1,
                     $formElement2,
                     $formElement3
        ));
        
        $this->_form = $form = new Events_Form_EventUpdate(
                array(
            'containerId' => 1,
            'containerType' => 'category',
            'model'         => $modelMock
        ));    
    }


    /**
     * Teste Model
     */
    public function testFormElements()
    {
        $form = $this->_form;
        
        // check form received
        $this->assertInstanceOf('Events_Form_EventUpdate',$form);
        $this->assertEquals(
            'Events_Form_EventCreate',
            get_parent_class($form)
        );
        $this->assertEquals(1, $form->getContainerId());
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array(
            'date','categoryId','test1','test2','test3','submit_update',
        );
        $this->assertEquals($expected, array_keys($form->getElements()));
    }
    
    
}
