<?php

/**
 * @group Forms
 * @group Events
 * 
 */
class FormEventCreateTest extends Pepit_Test_ZendTestCase
{
    /**
     * @var Zend_Form
     */
    protected $_form = null;
    
    protected $_formElements = array();
    
    function setUp()
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
        
        $this->_formElements = array(
            'test1' => array('class' =>'Zend_Form_Element_Text'),
            'test2' => array('class' =>'Zend_Form_Element_Text'),
            'test3' => array('class' 
                =>'Zend_Form_Element_Select','Multioptions' => array(
                    'value1'=> 'value1',
                    'value2' => 'value2')),
        );
        $formElements = array();
        foreach ($this->_formElements as $key => $element)
        {
            $class = $element['class'];
            $formElement = new $class($key);
            $formElement->setRequired(true);
            if (array_key_exists('Multioptions',$element))
            {
                $formElement->setMultiOptions($element['Multioptions']);
            }
            $formElements[] = $formElement;
        }
        
        $modelMock->expects($this->any())
             ->method('getFormElement')
             ->will($this->onConsecutiveCalls(
                     $formElements[0],
                     $formElements[1],
                     $formElements[2]
        ));
        
        $this->_form = $form = new Events_Form_EventCreate(
                array(
            'containerId' => 1,
            'containerType' => 'category',
            'model'         => $modelMock
        ));
        
        $this->keys = array_keys($this->_formElements);
    
    }
    
    /**
     * Test form elements
     */
    public function testForm()
    {
        $form = $this->_form;
        
        $this->assertInstanceOf('Zend_Form', $form);
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array_merge(
            array('date','categoryId'),
            array_keys($this->_formElements),
            array('submit_insert')
        );
        $this->assertEquals($expected, array_keys($form->getElements()));
    }
    
    /**
     * validate no input form
     */
    public function testValidateNoInput()
    {
        // define empty input
        $input = array();
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        $keys = $this->keys;
        // check errors
        $expected = array(
            'categoryId'                => array('isEmpty'),
            'date'                      => array('isEmpty'),
            $keys[0]                    => array('isEmpty'),
            $keys[1]                    => array('isEmpty'),
            $keys[2]                    => array('isEmpty'),
            'submit_insert'             => array()
        );
        
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * Test wrong input
     */
    public function testValidateWrongInput()
    {
       $keys = $this->keys;
       
       // prepare data
        $input = array(
            'categoryId'                => 'notinteger',
            'date'                      => 'falsedate',
            $keys[0]                    => 'notinthearray',
            $keys[1]                    => 'onevalue',
            $keys[2]                    => 'notinthearray',
            'submit_insert'             => 'anything'
        );
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        // check errors
        $expected = array(
            'categoryId'                    => array('notInt'),
            'date'                          => array('dateFalseFormat'),
            $keys[0]                        => array(),
            $keys[1]                        => array(),
            $keys[2]                        => array('notInArray'),
            'submit_insert'                 => array()
        );
        
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * validate correct data
     */
    public function testValidateCorrectInput()
    {
        $keys = $this->keys;
        
        // define input
        $input = array(
            'categoryId'                => 1,
            'date'                      => '01-01-2012',
            $keys[0]                    => 'testString',
            $keys[1]                    => 'value1',
            $keys[2]                    => 'value1',
            'submit_insert'             => $this->_form
                                                ->getTranslator()
                                                ->translate('action_save')
        );
        
        $target = array(
            'categoryId'                => 1,
            'date'                      => '01-01-2012',
            $keys[0]                    => 'testString',
            $keys[1]                    => 'value1',
            $keys[2]                    => 'value1',
        );
        
        
        ;
        //check validation
        $this->assertTrue($this->_form->isValid($input));
        
        //check button was pushed
         $this->assertTrue($this->_form->getElement('submit_insert')
                 ->isChecked());
         
         //check getValues
         unset($input['submit_insert']);
         $this->assertEquals($target,$this->_form->getValues());
    }
}
