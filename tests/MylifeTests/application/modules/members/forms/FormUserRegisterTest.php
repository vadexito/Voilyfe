<?php

/**
 * @group Forms
 * @group Members
 */
class FormUserRegisterTest extends Pepit_Test_ZendTestCase
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
        $optionMock = new stdClass();
        $optionMock->id = 1;
        $optionMock->value = 'FR';
        
        $repositoryMock = $this  
                        ->getMockBuilder('Doctrine\ORM\EntityRepository')
                        ->disableOriginalConstructor()
                        ->getMock();
        $repositoryMock ->expects($this->any())
                ->method('findAll')
                ->will($this->returnValue(array($optionMock)));
        $repositoryMock ->expects($this->any())
                ->method('findOneByUserName')
                ->will($this->returnValue(true));
        
        $emMock = $this  ->getMockBuilder('Doctrine\ORM\EntityManager')
                     ->disableOriginalConstructor()
                     ->getMock();
        $emMock ->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($repositoryMock));
        
        $this->_form = new Members_Form_UserRegister(
                array('entitymanager' => $emMock)
        );
        
    }
    
    /**
     * Test form elements
     */
    public function testForm()
    {
        // get Formular
        $form = $this->_form;
        
        // Tests classes
        $this->assertEquals('Members_Form_UserRegister', get_class($form));
        $this->assertEquals('Pepit_Form', get_parent_class($form));
        
        // test form attributes        
        $this->assertEquals('post', $form->getMethod());
    
        // test form elements
        $expected = array(
            'userName', 'userPassword','confirmPassword',
            'email','firstName','lastName' ,'countryId','submit_register'
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
            'userName'          => array('isEmpty'),
            'email'             => array('isEmpty'),
            'userPassword'      => array('isEmpty'),
            'confirmPassword'   => array('isEmpty'),
            'firstName'         => array(),
            'lastName'          => array(),
            'countryId'         => array(),
//            'languageId'        => array(),
            'submit_register'   => array(),
        );
        
        $this->assertEquals($expected, $this->_form->getErrors());
    }
    
    /**
     * Test wrong input
     */
    public function testValidateWrongInput()
    {
        // prepare data
        $input = array(
            'userName'          => 'mario',
            'email'             => 'abc',
            'userPassword'      => 'gehe',
            'confirmPassword'   => 'an',
            'firstName'         => str_pad('abc', 100, 'def'),
            'lastName'          => str_pad('abc', 100, 'def'),
            'countryId'         => 'falsecountry',
//            'languageId'        => 'falselanguage',
            'submit_register'   => 'submit_register',
        );
        
        // check validation
        $this->assertFalse($this->_form->isValid($input));
        
        // check errors
        $expected = array(
            'userName'                  => array(),//validator disabled through the mock
            'email'                     => array('emailAddressInvalidFormat'),
            'userPassword'              => array('stringLengthTooShort','notMatch'),
            'confirmPassword'           => array('stringLengthTooShort'),
            'firstName'                 => array('stringLengthTooLong'),
            'lastName'                  => array('stringLengthTooLong'),
            'countryId'                 => array('notInArray'),
//            'languageId'                => array('notInArray'),
            'submit_register'           => array(),
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
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1,
            'submit_register'           => $this->_form
                                                ->getTranslator()
                                                ->translate('action_register')
        );
        
        // check validation
        $this->assertTrue($this->_form->isValid($input));
        
        //check button was pushed
        
         $this->assertTrue($this->_form->getElement('submit_register')
                 ->isChecked());
         
         //check getValues
         unset($input['submit_register']);
         $this->assertEquals($input,$this->_form->getValues());
    }
}
