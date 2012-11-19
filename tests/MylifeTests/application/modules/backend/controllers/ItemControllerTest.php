<?php

/**
 * @group Controllers
 * @group Backend
 */

class ItemControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    protected $_model;
    
    //override setup in order to cancel any file creation
    static public function setUpBeforeClass()
    { 
    }

    public function setUp()
    {
        parent::setUp();
        
        //create admin
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
            $this->em,
            'userName',
            $this->memberPass,true,true
        );
        $this->member->role = "admin";
        
        TestHelpersDoctrine::initBaseFormItems($this->em);
        
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $this->_model = new Backend_Model_Items($this->em);
    }
    
    public function testIndex()
    {
        $this->dispatch($this->url(array(
            'controller' => 'item',
            'action' => 'index'
        ),'backend'));
        
        $this->assertNotRedirect();
        
        $this->assertEquals('index.phtml',$this->vr->getViewScript());
    }
    
    
    
    
    
    /**
     *
     * @dataProvider providerInputItemCreateSuccess
     */
    
    public function testItemCreatePostSentSuccess($input)
    {
        // Prepare request
        $this->request->setMethod('POST');
        $time = new \DateTime();
        $name = 'testnewitem'.$time->getTimestamp();
        
        $formElementClass = $this->em
                        ->getRepository('ZC\Entity\FormElementClass')
                        ->findOneByName($input['formElementClass']);
        unset($input['formElementClass']);
        
        
        $input = array_merge($input,array(
            'name'                  => $name,
            'formElementClassId'    => $formElementClass->id,
            'submit_insert'         => $this->vr->view->translate('action_create'),
        ));
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(array(
            'controller' => 'item',
            'action' => 'create'
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item created successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertController('item');
        $this->assertAction('create');
        
        //check file
        $path = $this->_model->getFormElementPath($name);
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
        
        //check data base
        $this->em->clear();
        $itemDB = $this->em->getRepository('ZC\Entity\Item')->find(1);
        
        $this->assertInstanceOf('ZC\Entity\Item',$itemDB);
        $this->assertEquals($input['name'],$itemDB->name);
        $this->assertEquals($input['itemType'],$itemDB->itemType);
        $this->assertEquals(
            $formElementClass->name,
            $itemDB->formElementClass->name
        );
        if (!array_key_exists('formFilterIds',$input))
        {
            $input['formFilterIds'] = array();
        }
        $this->assertEquals(count($input['formFilterIds']),$itemDB->formFilters->count());
        if (!array_key_exists('formValidatorIds',$input))
        {
            $input['formValidatorIds'] = array();
        }
        $this->assertEquals(count($input['formValidatorIds']),$itemDB->formValidators->count());
        $this->assertEquals($input['formLabel'],$itemDB->formLabel);
        $this->assertEquals(
            $input['nullableSQL'] === '1',
            $itemDB->nullableSQL
        );
        $this->assertEquals(
            $input['formRequired'] === '1',
            $itemDB->formRequired
        );
        $this->assertEquals($input['associationType'],$itemDB->associationType);
        $this->assertEquals($input['typeSQL'],$itemDB->typeSQL);
        
        $multioptions = explode(',',$input['formMultioptions']);
        if ($input['formMultioptions'] === '')
        {
            $multioptions = array();
        }
        $this->assertEquals(
                count($multioptions),
                $itemDB->itemRows->count()
        );
        $optionsDB = array();
        foreach ($itemDB->itemRows as $option)
        {
             $optionsDB[] = $option->value;
        }
        $this->assertEquals($multioptions,$optionsDB);
    }
    
    public function providerInputItemCreateSuccess()
    {
        return array(
            array(array(
                    'itemType'              => 1,
                    'formElementClass'      => 'Pepit_Form_Element_Text',
                    'formFilterIds'         => array(1),
                    'formValidatorIds'      => array(2),
                    'formLabel'             => 'new item:',
                    'formMultioptions'      => 'value1,value2,value3',
                    'formRequired'          => '1',
                    'nullableSQL'           => '0',
                    'typeSQL'               => 'string',
                    'sizeSQL'               => '',
                    'associationType'       => '0' // mapfield
                    )
                ),
            array(array(
                    'itemType'              => 1,
                    'formElementClass'      => 'Pepit_Form_Element_MultiCheckbox',
                    'formFilterIds'         => array(1,2),
                    'formValidatorIds'      => array(2),
                    'formLabel'             => 'new item:',
                    'formMultioptions'      => 'value1,value2,value3',
                    'formRequired'          => '1',
                    'nullableSQL'           => '0',
                    'typeSQL'               => 'string',
                    'sizeSQL'               => '',
                    'associationType'       => 8 //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY,
                    )
                ),
            array(array(
                    'itemType'              => 1,
                    'formElementClass'      => 'Pepit_Form_Element_Select',
                    'formLabel'             => 'new item:',
                    'formMultioptions'      => '',
                    'formRequired'          => '1',
                    'nullableSQL'           => '0',
                    'typeSQL'               => 'string',
                    'sizeSQL'               => '',
                    'associationType'       => 4 //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY,
                    )
                ),
        );
    }
    
    public function testItemCreatePostSentFailure()
    {
        // Prepare request
        $this->request->setMethod('POST');
        $time = new \DateTime();
        $name = 'test'.$time->getTimestamp();
        
        $input = array(
            'name'                  => $name,
            'formElementClassId'    => 1,
            'itemType'              => 1,
            'formLabel'             => 'new item:',
            'formMultioptions'    => '',
            'formRequired'          => '1',
            'nullableSQL'           => '0',
            'typeSQL'               => 'string',
            'sizeSQL'               => 'notaninteger',
            'associationType'       => 'notnumber', 
            'submit_insert'         => $this->vr->view->translate('action_create'),
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(array(
            'controller' => 'item',
            'action' => 'create'
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item created successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        $this->assertController('item');
        $this->assertAction('create');
        $this->assertNotRedirect();
        
        //check populating
        $form = $this->vr->view->form;
                
        unset($input['submit_insert']);
        
        $populated = $input;
        
        foreach ($populated as $key => $value)
        {
            $this->assertEquals($value,$form->getElement($key)->getValue());
        }
        
       
    }
    
    public function testItemEditPostSent()
    {
        $itemType = 'String';

        //create item row in table
        $item = call_user_func_array(
            array('TestHelpersDoctrine','createItem'.$itemType),
            array($this->em)
        );
        $itemId = $item->id;
        $name = $item->name;
        
        //create file item
        $this->_model->createNewFormElement($item);
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        //name is changed only for testing (to avoid the initial require_once)
        $input = array(
            'name'                  => $name,
            'itemType'              => 1,
                    'formElementClassId'    => $this->em->getRepository('ZC\Entity\FormElementClass')
                                        ->findOneByName('Pepit_Form_Element_Text')->id,
            'formFilterIds'         => array(),
            'formValidatorIds'      => array(2),
            'formLabel'             => 'new item label:',
            'formMultioptions'      => '',
            'formRequired'          => '0',
            'nullableSQL'           => '1',
            'typeSQL'               => 'string',
            'sizeSQL'               => 255,
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY,
            'submit_update'         => $this->vr->view->translate('action_save'),
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'item',
                'action' => 'edit',
                'entityId' => $itemId),
            'backend'
        ));
        $this->assertController('item');
        $this->assertAction('edit');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller' => 'item',
            'action' => 'index'
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check category row
        $this->em->clear();
        $itemDB = $this->em ->getRepository('ZC\Entity\Item')
                                ->find($itemId);
        
        $this->assertNotNull($itemDB);
        $this->assertEquals($input['name'],$itemDB->name);
        $this->assertEquals($input['itemType'],$itemDB->itemType);
        $this->assertEquals($input['formElementClassId'],$itemDB->formElementClass->id);
        
        $this->assertEquals(count($input['formFilterIds']),$itemDB->formFilters->count());
        $this->assertEquals(count($input['formValidatorIds']),$itemDB->formValidators->count());
        $this->assertEquals($input['formLabel'],$itemDB->formLabel);
        $this->assertEquals($input['associationType'],$itemDB->associationType);
        $this->assertEquals($input['typeSQL'],$itemDB->typeSQL);
        $this->assertEquals($input['sizeSQL'],$itemDB->sizeSQL);
        
        $this->assertEquals(
            $input['nullableSQL'] === '1',
            $itemDB->nullableSQL
        );
        $this->assertEquals(
            $input['formRequired'] === '1',
            $itemDB->formRequired
        );
        $multioptions = array();
        if ($input['formMultioptions'])
        {
            $multioptions = explode(',',$input['formMultioptions']);
        }
        $this->assertEquals(
                count($multioptions),
                $itemDB->itemRows->count()
        );
        $optionsDB = array();
        foreach ($itemDB->itemRows as $option)
        {
             $optionsDB[] = $option->value;
        }
        $this->assertEquals($multioptions,$optionsDB);
        
        
        //check file
        $path = $this->_model->getFormElementPath($input['name']);
        $this->assertFileExists($path);
        $this->_unlinkTeardown[] = $path;
    }
    
    public function testItemEditSuccessfullyPostSentNotValid()
    {
        $itemType = 'String';

        //create item row in table
        $item = call_user_func_array(
            array('TestHelpersDoctrine','createItem'.$itemType),
            array($this->em)
        );
        $itemId = $item->id;
        $name = $item->name;
        
        //create file item
        $this->_model->createNewFormElement($item);
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        //name is changed only for testing (to avoid the initial require_once)
        $input = array(
            'name'                  => $name,
            'itemType'              => 1,
            'formElementClassId'    => 1,
            'formFilterIds'         => array(),
            'formValidatorIds'      => array(2),
            'formLabel'             => 'new item label:',
            'formMultioptions'      => '',
            'formRequired'          => '0',
            'nullableSQL'           => '1',
            'typeSQL'               => 'string',
            'sizeSQL'               => 'notinteger',
            'associationType'       => 2,
            'submit_update'         => $this->vr->view->translate('action_save'),
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'item',
                'action' => 'edit',
                'entityId' => $itemId),
            'backend'
        ));
        $this->assertController('item');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check populating
        $form = $this->vr->view->form;
                
        unset($input['submit_update']);
        
        $populated = $input;
        
        foreach ($populated as $key => $value)
        {
            $this->assertEquals($value,$form->getElement($key)->getValue());
        }
    }
    
    public function testItemEditPostNotSent()
    {
        $itemType = 'String';

        //create item row in table
        $item = call_user_func_array(
            array('TestHelpersDoctrine','createItem'.$itemType),
            array($this->em)
        );
        $itemId = $item->id;
        
        //create file item
        $this->_model->createNewFormElement($item);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'item',
                'action' => 'edit',
                'entityId' => $itemId),
            'backend'
        ));
        $this->assertController('item');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertFalse(in_array(
                    $this->vr->view->translate('Item updated successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check populating
        $form = $this->vr->view->form;
                
        $singleProperties = array('name','itemType','formLabel','formRequired',
            'nullableSQL','typeSQL','sizeSQL','associationType');
        foreach ($singleProperties as $property)
        {
            $this->assertEquals($item->$property,$form->getElement($property)->getValue());
        }
        $this->assertEquals($item->formElementClass->id,$form->getElement('formElementClassId')->getValue());
        $this->assertEquals($item->formFilters->count(),count($form->getElement('formFilterIds')->getValue()));
        $this->assertEquals($item->formValidators->count(),count($form->getElement('formValidatorIds')->getValue()));
    }
    
    /**
     *
     * @dataProvider providerItemTypes
     */
    
    public function testItemDeletePostSent($type)
    {
        $item = call_user_func_array(
            array('TestHelpersDoctrine', 'createItem'.$type),
            array($this->em)
        );
        
        $this->_model->createNewFormElement($item);
        
        $itemId = $item->id;
        $path = $this->_model->getFormElementPath($item->name);
        
        // Prepare data for login
        $this->request->setMethod('POST');
        
        $input = array(
            'submit_delete' => $this->vr->view->translate('action_delete')
        );
        
        $this->request->setPost($input);
        
        // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'item',
                'action' => 'delete',
                'entityId' => $itemId),
            'backend'
        ));
        $this->assertController('item');
        $this->assertAction('delete');
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(array(
            'controller' => 'item',
            'action' => 'index',
        ),'backend'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('Item deleted successfully.'),
                    $fm->getCurrentMessages()
        ));
        
        //check item row not existing
        $itemDB = $this->em ->getRepository('ZC\Entity\Item')
                                ->find($itemId);
        $this->assertNull($itemDB);
        $generalizedItemDB = $this->em ->getRepository('ZC\Entity\GeneralizedItem')
                                ->find($itemId);
        $this->assertNull($generalizedItemDB);
        
        //check form element file
        $this->assertFileNotExists($path);
    }
    
    public function testItemDeleteNotPostSent()
    {
        //create item
        $category = $this->initCategoryTest();
        $item = $category->items[0];
        $itemId = $item->id;
        $path = $this->_model->getFormElementPath($item->name);
        
         // execute create category
        $this->dispatch($this->url(
            array(
                'controller' => 'item',
                'action' => 'delete',
                'entityId' => $itemId),
            'backend'
        ));
        $this->assertController('item');
        $this->assertAction('delete');
        $this->assertNotRedirect();
        
        //check element is still in database
        $this->em->clear();
        $itemDB = $this->em ->getRepository('ZC\Entity\Item')
                            ->find($itemId);
        $this->assertEquals($itemId,$itemDB->id);
        
        //check form element file still exists
        $this->assertFileExists($path);
    }
    
    public function providerItemTypes()
    {
        return array(
//            array('string'),
//            array('OneToMany'),
            array('ManyToOne'),
//            array('OneToOne'),
//            array('ManyToMany'),
        );
    }
}
