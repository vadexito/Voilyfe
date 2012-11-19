<?php

/**
 * @group Models 
 * @group Backend
 */


class BackendModelItemsTest extends Pepit_Test_DoctrineTestCase
{
    protected $_repository;
    
    protected $_model;
    
    
    public function setUp()
    {
        parent::setUp();
        
        //mocking member authorization
        $auth = Zend_Auth::getInstance();
        $memberMock = new stdClass();
        $memberMock->id = 1;
        $auth->getStorage()->write($memberMock);
        
        $this->_repository = $this->em->getRepository('ZC\Entity\Item');
        $this->_model = new Backend_Model_Items();
        
    }
    
    public function testCreateNewItemFromForm()
    {
        TestHelpersDoctrine::createFormElementClasses($this->em);
        TestHelpersDoctrine::createFormFilters($this->em);
        TestHelpersDoctrine::createFormValidators($this->em);
        
        $formElementClass = $this->em
                                 ->getRepository('ZC\Entity\FormElementClass')
                                 ->find(1);
        $formFilters1 = $this->em
                                 ->getRepository('ZC\Entity\FormFilter')
                                 ->find(1);
        $formFilters2 = $this->em
                                 ->getRepository('ZC\Entity\FormFilter')
                                 ->find(2);
        $formValidator = $this->em
                                 ->getRepository('ZC\Entity\FormValidator')
                                 ->find(1);
        
        
        $input = array(
            'name'                  => 'new item',
            'itemType'              => \ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_SINGLE_ITEM,
            'formElementClassId'    => $formElementClass->id,
            'formFilterIds'         => array($formFilters1->id,$formFilters2->id),
            'formValidatorIds'      => array($formValidator->id),
            'formLabel'             => 'new item:',
            'formMultioptions'      => 'multi1,multi2',
            'formRequired'          => '1',
            'nullableSQL'           => '0',
            'typeSQL'               => 'associationField',
            'sizeSQL'               => '',
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
        );
        
       
        $item = $this->_model->createEntityFromForm($input);
        $this->em->persist($item);
        $this->em->flush();
        
        $itemDB = $this->_repository->findOneByName($input['name']);
        
        $this->assertEquals($input['name'],$itemDB->name);
        $this->assertEquals($input['itemType'],$itemDB->itemType);
        $this->assertEquals($formElementClass->name,$itemDB->formElementClass->name);
        $this->assertEquals($input['formLabel'],$itemDB->formLabel);
        $this->assertTrue($itemDB->formRequired);
        $this->assertFalse($itemDB->nullableSQL);
        $this->assertEquals($input['typeSQL'],$itemDB->typeSQL);
        $this->assertEquals((int)$input['sizeSQL'],$itemDB->sizeSQL);
        $this->assertEquals($input['associationType'],$itemDB->associationType);
        $this->assertEquals($formFilters1->name,$itemDB->formFilters[0]->name);
        $this->assertEquals($formFilters2->name,$itemDB->formFilters[1]->name);
        $this->assertEquals($formValidator->name,$itemDB->formValidators[0]->name);
        $this->assertEquals('multi1',$itemDB->itemRows[0]->value);
        $this->assertEquals('multi2',$itemDB->itemRows[1]->value);
    }
    
    /**
     *
     * @dataProvider providerFormKeys
     */
    public function testGetItemNameFromFormItemKey($formKey,$itemAssociationType,$target)
    {
        $this->assertEquals(
            $this->_model
                 ->getItemNameFromFormItemKey($formKey,$itemAssociationType),
            $target
        );
    }
    
    public function providerFormKeys()
    {
        return array(
            // mapfield case
            array('test',0,'test'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
            array('testId',2,'test'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
            array('testIds',8,'tests'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
            array('tests',4,'tests'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE
            array('test',1,'test'),
        );
    }
    
    
    /**
    * @dataProvider providerFormKeysErrors
    * @expectedException Pepit_Model_Exception
    * @expectedExceptionMessage Name of form key invalid forassociation type considered
    */
     
    public function testGetItemNameFromFormItemKeyThrowExceptions(
                                        $formKey,$itemAssociationType)
    {
        $this->_model->getItemNameFromFormItemKey(
                $formKey,
                $itemAssociationType
        );
    }
    
    public function providerFormKeysErrors()
    {
        return array(
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
            array('test',2),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
            array('test',8),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
            array('test',4),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE
            array('tests',1),
        );
    }
    
    /**
     *
     * @dataProvider providerItemNames
     */
    public function testGetFormItemName($itemName,$itemAssociationType,$target)
    {
        $this->assertEquals(
            $this->_model
                 ->getFormItemName($itemName,$itemAssociationType),
            $target
        );
    }
   
    public function providerItemNames()
    {
        return array(
            // mapfield case
            array('test',0,'test'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE
            array('test',2,'testId'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
            array('tests',8,'testIds'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
            array('tests',4,'tests'),
            //\Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE
            array('test',1,'test'),
        );
    }
    
    public function providerItemTypes()
    {
        return array(
            array('String',array(
                                'formHelper' => 'formText'
                            )
            ),
            array('OneToOne',array(
                                'formHelper' => 'formSelect'
                            )
            ),
            array('OneToMany',array(
                                'formHelper' => 'formText'
                            )
            ),
            array('ManyToOne',array(
                                'formHelper' => 'formSelect'
                            )
            ),
            array('ManyToMany',array(
                                'formHelper' => 'formMultiCheckbox'
                            )
            ),
        );
    }

    /**
     *
     * @dataProvider providerItemTypes
     * 
     */
    public function testCreateNewFormElement($itemType,$target)
    {
        TestHelpersDoctrine::createFormElementClasses($this->em);
        $methodForCreating = 'createItem'.$itemType;
        $item = call_user_func_array(
            array('TestHelpersDoctrine',$methodForCreating),
            array($this->em)
        );
        $name = $item->name;
        
        $this->_model->createNewFormElement($item);
        $path = Backend_Model_Items::getFormElementPath($name);
        $this->assertTrue(file_exists($path));
        $this->_unlinkTeardown[] = $path;
        
        $formElementName = $this->_model->getFormElementClassName($item->name);
        $formItemName = Backend_Model_Items::getFormItemName(
            $name,
            $item->associationType
        );
        
        $formElement = new $formElementName($formItemName);
        $this->assertInstanceOf('Zend_Form_Element',$formElement);
        $this->assertEquals($formItemName,$formElement->getName());
        $this->assertEquals($item->formRequired === '1',$formElement->isRequired());
        $this->assertEquals($target['formHelper'],$formElement->getAttrib('helper'));
        
        
        foreach ($item->itemRows as $option)
        {
            $this->assertTrue(in_array(
                    $option->value,
                    $formElement->getAttrib('options')
            ));
        }
    }
}
