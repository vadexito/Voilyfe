<?php

/**
 * @group Forms
 * @group Backend
 */
class FormCreateItemGroupTest extends Pepit_Test_ControllerTestCaseWithDoctrine
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
        TestHelpersDoctrine::createItemString($this->em);
        
        // Instantiate form instance
        $this->_form = new Backend_Form_ItemGroupCreate(
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
        $this->assertInstanceOf('Backend_Form_ItemGroupCreate',$form);
        $this->assertInstanceOf('Zend_Form', $form);
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array(
            'name','formElementClassId','formRequired','associationType',
            'itemType','itemIds','identifierItemId','submit_insert'
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
            'formElementClassId'    => array('isEmpty'),
            'formRequired'          => array(),
            'associationType'       => array('isEmpty'),
            'itemType'              => array('isEmpty'),
            'itemIds'               => array('isEmpty'),
            'identifierItemId'      => array('isEmpty'),
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
            'formRequired'          => '',
            'associationType'       => '',
            'itemType'              => 'falsetype',
            'itemIds'               => 'notarray',
            'identifierItemId'      => 'notinteger',
            'submit_insert'         => 'ok',
        );
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        // check errors
        $expected = array(
            'name'                  => array(),
            'formElementClassId'    => array('notInArray'),
            'formRequired'          => array(),
            'associationType'       => array('isEmpty'),
            'itemType'              => array('notInArray'),
            'itemIds'               => array('notInArray'),
            'identifierItemId'      => array('notInArray'),
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
            'formElementClassId'    => $this->em
                            ->getRepository('ZC\Entity\FormElementClass')
                            ->findOneByName('Pepit_Form_Element_Select')->id,
            'formRequired'          => '1',
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY,
            'itemType'              => '2',
            'itemIds'               => array('1'),
            'identifierItemId'      => '1',
            'submit_insert'         => $this->vr->view->translate('action_create'),
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
