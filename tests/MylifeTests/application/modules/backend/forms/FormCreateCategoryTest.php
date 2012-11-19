<?php

/**
 * @group Forms
 * @group Backend
 */
class CreateCategoryTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    /**
     * @var Zend_Form
     */
    protected $_form = null;
    
    private $_category;

    /**
     * set up the environnement
     */
    function setUp()
    {
        parent::setUp();
        
        //initialise doctrine database (before form for in-array check)
        $this->_category = TestHelpersDoctrine::getEntity(
                $this->em,
                '\ZC\Entity\Category',
                array('name'=>'test'),
                true,
                true
        );
        $itemString = TestHelpersDoctrine::createItemString($this->em);
        $this->_category->addItem($itemString);
        
        // Instantiate form instance
        $this->_form = new Backend_Form_CategoryCreate(array(
            'entitymanager' => $this->em,
        ));
    }
    
    /**
     * Test form elements
     */
    public function testForm()
    {
        // get Formular
        $form = $this->_form;
        
        // Tests classes
        $this->assertInstanceOf('Backend_Form_CategoryCreate', $form);
        $this->assertInstanceOf('Zend_Form', $form);
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array(
            'name','itemIds','categoryIds','submit_insert'
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
        
        // check errors
        $expected = array(
            'name'              => array('isEmpty'),
            'itemIds'           => array(),
            'categoryIds'       => array(),
            'submit_insert'     => array(),
        );
        
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * Test wrong input
     * @dataProvider providerWrong_TestNameFilters
     */
    public function testValidateWrongInput($suffix)
    {
        // prepare data
        //testing strToLower filter
        $input = array(
            'name'              => strtoupper($this->_category->name).$suffix,
            'itemIds'           => array(),
            'categoryIds'       => array(),
            'submit_insert'     => 'action_create',
        );
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        // check errors
        $expected = array(
            'name'                      => array('notUnique'),
            'itemIds'                   => array(),
            'categoryIds'               => array(),
            'submit_insert'             => array(),
        );
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    public function providerWrong_TestNameFilters()
    {
        return array(
            array(''),
            array('     '), //test StringTrim
            array('   ;:;:;!  '), //test Alnum
            array('<br/>'), //test StripTags
        );
    }
    
    /**
     * validate correct data
     */
    public function testValidateCorrectInput()
    {
        
        // define input
        $input = array(
            'name'              => 'differentname',
            'itemIds'           => array($this->_category->items[0]->id),
            'categoryIds'       => array(),
            'submit_insert'     => $this->vr->view->translate('action_create')
        );
        
        // check validation
        $this->assertTrue($this->_form->isValid($input));
        
        //check button was pushed
         $this->assertTrue($this->_form->getElement('submit_insert')
                 ->isChecked());
         
         //check getValues
         unset($input['submit_insert']);
         $this->assertEquals($input,$this->_form->getValues());
    }
}
