<?php


/**
 * @group Forms
 * @group Access
 */
class FormUserLoginTest extends Pepit_Test_ZendTestCase
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
        
        // Instantiate form instance
        $this->_form = new Access_Form_UserLogin();
        
        $this->vr = Zend_Controller_Action_HelperBroker::getStaticHelper(
                'viewRenderer'
        );
        
    }
    
    /**
     * Test formular elements
     */
    public function testForm()
    {
        // get Formular
        $form = $this->_form;
        
        // Tests classes
        $this->assertEquals('Access_Form_UserLogin', get_class($form));
        $this->assertEquals('Pepit_Form', get_parent_class($form));
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
        
        // test form elements
        $expected = array(
            'userName', 'userPassword','submit_login', 'rememberMe'
        );
        $this->assertEquals($expected, array_keys($form->getElements()));
    }
        
    /**
     * Test username element
     */
    public function testElementUserName()
    {
        // get element
        $element = $this->_form->getElement('userName');
        
        // Test options and attribute
        $this->assertEquals('Pepit_Form_Element_Text', $element->getType());
        $this->assertEquals('userName', $element->getId());
        $this->assertTrue($element->isRequired());
        
        // Test filters
        $filters = $element->getFilters();
        $this->assertTrue(isset($filters['Zend_Filter_StringTrim']));
        $this->assertTrue(isset($filters['Zend_Filter_StripTags']));
        
        // Test validators
        $validators = $element->getValidators();
        $this->assertTrue(isset($validators['Zend_Validate_NotEmpty']));
    }
    
    /**
     * Test password element
     */
    public function testElementUserPassword()
    {
        // get element
        $element = $this->_form->getElement('userPassword');
        
        // Test options and attribute
        $this->assertEquals('Pepit_Form_Element_Password', $element->getType());
        $this->assertEquals('userPassword', $element->getId());
        $this->assertTrue($element->isRequired());
        
        // Test filters
        $filters = $element->getFilters();
        $this->assertTrue(isset($filters['Zend_Filter_StringTrim']));
        $this->assertTrue(isset($filters['Zend_Filter_StripTags']));
        
        // Test validators
        $validators = $element->getValidators();
        $this->assertTrue(isset($validators['Zend_Validate_NotEmpty']));
    }
    
    
    /**
     * Test element for submit button
     */
    public function testElementSubmit()
    {
        // get element
        $element = $this->_form->getElement('submit_login');
        
        // Prüfe Optionen und Attribute
        $this->assertEquals('Pepit_Form_Element_Submit', $element->getType());
        $this->assertEquals('submit_login', $element->getId());
        $this->assertEquals($this->vr->view->translate('action_login'), $element->getLabel());
        $this->assertFalse($element->isRequired());
    }
    
    /**
     * test for no input
     */
    public function testValidatingNoInput()
    {
        // define data
        $input = array();
        
        // Test global validation
        $this->assertFalse($this->_form->isValid($input));
        
        // Define expected errors
        $expected = array(
            'userName'          => array('isEmpty'),
            'userPassword'      => array('isEmpty'),            
            'rememberMe'        => array(),            
            'submit_login'      => array(),
        );
        
        //check errors
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * test for not validated input
     */
    public function testValidatingWrongInput()
    {
        // define data
        $input = array(
            'userName'          => '',
            'userPassword'      => 'secret',
            'rememberMe'        => '',
            'submit_login'      => 'submit_login',
        );
        
        //  Test global validation
        $this->assertFalse($this->_form->isValid($input));
        
        // define expected errors
        $expected = array(
            'userName'      => array('isEmpty'),
            'userPassword'  => array(),
            'rememberMe'    => array(),
            'submit_login'  => array(),
        );
        
        //check errors
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * test valid input
     */
    public function testValidatingCorrectInput()
    {
        // define data
        $input = array(
            'userName'      => 'test',
            'userPassword'  => 'secret',
            'rememberMe'    => 1,
            'submit_login'  => $this->vr->view->translate('action_login'),
        );
        
        //  Test global validation
        $this->assertTrue($this->_form->isValid($input));
        
        //check button was pushed
         $this->assertTrue($this->_form->getElement('submit_login')
                 ->isChecked());
         
    }
}
