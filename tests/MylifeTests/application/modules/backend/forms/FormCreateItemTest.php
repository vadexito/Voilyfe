<?php

/**
 * @group Forms
 * @group Backend
 */
class FormCreateItemTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    /**
     * @var Zend_Form
     */
    protected $_form = null;

    /**
     * set up the environnement
     */
    function setUp()
    {
        parent::setUp();
        
        TestHelpersDoctrine::initBaseFormItems($this->em);

        // Instantiate form instance
        $this->_form = new Backend_Form_ItemCreate(
                array(
                    'entitymanager' => $this->em
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
        $this->assertInstanceOf('Backend_Form_ItemCreate',$form);
        $this->assertInstanceOf('Zend_Form', $form);
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array(
            'name','formElementClassId','formRequired','associationType','itemType','typeSQL','sizeSQL',
            'nullableSQL','formLabel','formMultioptions','formFilterIds',
            'formValidatorIds','submit_insert'
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
            'name'                  => array('isEmpty'),
            'itemType'                  => array('isEmpty'),
            'formElementClassId'    => array('isEmpty'),
            'formFilterIds'         => array(),
            'formValidatorIds'      => array(),
            'formLabel'             => array('isEmpty'),
            'formMultioptions'      => array(),
            'formRequired'          => array(),
            'nullableSQL'           => array(),
            'typeSQL'               => array(),
            'sizeSQL'               => array(),
            'associationType'       => array('isEmpty'),
            'submit_insert'         => array(),
        );
        
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * Test wrong input
     */
    public function testValidateWrongInput()
    {
        //initialise doctrine database
        $string = TestHelpersDoctrine::createItemString($this->em);
        
        // prepare data
        $input = array(
            'name'                  => $string->name,
            'formElementClassId'    => 'notgood',
            'formFilterIds'         => 'notgood',
            'formValidatorIds'      => 'notgood',
            'formLabel'             => '',
            'formMultioptions'      => '',
            'formRequired'          => '',
            'nullableSQL'           => '',
            'typeSQL'               => 'notgood',
            'sizeSQL'               => 'not integer',
            'associationType'       => '',
            'submit_insert'         => 'ok',
        );
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        // check errors
        $expected = array(
            'name'                  => array(),
            'itemType'              => array('isEmpty'),
            'formElementClassId'    => array('notInArray'),
            'formFilterIds'         => array('notInArray'),
            'formValidatorIds'      => array('notInArray'),
            'formLabel'             => array('isEmpty'),
            'formMultioptions'      => array(),
            'formRequired'          => array(),
            'nullableSQL'           => array(),
            'typeSQL'               => array('notInArray'),
            'sizeSQL'               => array('notInt'),
            'associationType'       => array('isEmpty'),
            'submit_insert'         => array(),
        );
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * validate correct data
     */
    public function testValidateCorrectInput()
    {
        
        // define input
        $input = array(
            'name'                  => 'newitem',
            'itemType'              => \ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_SINGLE_ITEM,
            'formElementClassId'    => '1',
            'formFilterIds'         => array('1','2'),
            'formValidatorIds'      => array('1','2'),
            'formLabel'             => 'new item:',
            'formMultioptions'      => 'el1,el2',
            'formRequired'          => '1',
            'nullableSQL'           => '1',
            'typeSQL'               => 'string',
            'sizeSQL'               => '',
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY,
            'submit_insert'         => $this->vr->view->translate('action_create'),
        );
        //$input = $this->_form->isValid($input);
        //var_dump($this->_form->getErrors());die;
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
