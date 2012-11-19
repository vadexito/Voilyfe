<?php

/**
 * @group Models
 * @group Pepit
 */
class PepitModelDoctrine2Test extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    protected $_model;
    
    /**
     *
     * @dataProvider dataProviderItemTypeCaseManyToXField
     */
    
    public function testGetArrayforFormFromManyToXField($type,
            $entityMultioptionIds)
    {
        $nameConst = strtoupper(\Doctrine\Common\Util\Inflector::tableize($type));
        $ref = new ReflectionClass('\Doctrine\ORM\Mapping\ClassMetadataInfo');
        $associationTypeCode = $ref->getConstant($nameConst);
        
        $item = call_user_func_array(
                array('TestHelpersDoctrine','createItem'.$type),
                array($this->em)
        );
        $itemName = $item->name;
        $formItemName = Backend_Model_Items::getFormItemName(
            $itemName,
            $associationTypeCode
        );
        
        $arrayForForm = 
            Pepit_Model_Doctrine2::getArrayForFormFromEntityWithSingleValueFields(
                $this->getEntityManagerMock(
                    $associationTypeCode,
                    $itemName
                ),    
                $this->getEntityMock(
                    $associationTypeCode,
                    $entityMultioptionIds,
                    $itemName
                ),
                array($formItemName)
            );
        
        $target = array(
            $formItemName => $entityMultioptionIds
        );
        
        $this->assertEquals($target,$arrayForForm);
    }
    
    public function dataProviderItemTypeCaseManyToXField()
    {
        return array(
            array(
                'ManyToOne',
                1,
            ),
            array(
                'ManyToMany',
                array(1,2)
           )
        );
    }
    
    protected function getEntityMock($associationTypeCode,
                                           $entityMultioptionIds,$itemName)
    {
        $entityMock = new stdClass();
        
        if ($associationTypeCode === 
                \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE)
        {
            $formMultioptionMock = new stdClass();
            $formMultioptionMock->id = $entityMultioptionIds;
            $entityMock->$itemName = $formMultioptionMock;
        }
        if ($associationTypeCode === 
                \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY)
        {
            
            $formMultioptions = array();
            foreach ($entityMultioptionIds as $id)
            {
                $formMultioptionMock = new stdClass();
                $formMultioptionMock->id = $id;
                $formMultioptions[] = $formMultioptionMock;
            }
            $entityMock->$itemName = $formMultioptions;
        }
        
        return $entityMock;
    }
    
    protected function getEntityManagerMock($associationTypeCode,$itemName)
    {
        $metadataFactoryMock = $this->getMock(
            'Doctrine\ORM\Mapping\ClassMetadataFactory'
        );
        
        $metadataMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
                              ->disableOriginalConstructor()
                              ->getMock();
        if ($associationTypeCode === '0')
        {
            $metadataMock->fieldMappings = array($itemName);
        }
        else
        {
            $metadataMock->associationMappings = array(
            $itemName => array(
                    'type' => $associationTypeCode
            ));
        }
        
        $metadataFactoryMock->expects($this->any())
                ->method('getMetadataFor')
                ->will($this->returnValue($metadataMock));
        
        $emMock = $this ->getMockBuilder('\Doctrine\ORM\EntityManager')
                    ->disableOriginalConstructor()
                    ->getMock();
        $emMock->expects($this->any())
                ->method('getMetadataFactory')
                ->will($this->returnValue($metadataFactoryMock));
        
        return $emMock;
    }
    
    /**
     *
     * @dataProvider dataProviderItemTypeCaseOneToXField
     */
    
    public function testGetArrayforFormFromOneToXField($type,
            $entityPropertyValue,$targetValue)
    {
        $nameConst = strtoupper(\Doctrine\Common\Util\Inflector::tableize($type));
        $ref = new ReflectionClass('\Doctrine\ORM\Mapping\ClassMetadataInfo');
        $associationTypeCode = $ref->getConstant($nameConst);
        
        $item = call_user_func_array(
                array('TestHelpersDoctrine','createItem'.$type),
                array($this->em)
        );
        $itemName = $item->name;
        
        $entityMock = new stdClass();
        
        if ($associationTypeCode === 
                \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE)
        {
           $entityMock->$itemName = $entityPropertyValue;
        }
        
        if ($associationTypeCode === 
                \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY)
        {
            $formOptions = array();
            foreach ($entityPropertyValue as $value)
            {
                $formOptionMock = new stdClass();
                $formOptionMock->value = $value;
                $formOptions[] = $formOptionMock;
            }
            $entityMock->$itemName = $formOptions;
        }
        
        $formItemName = Backend_Model_Items::getFormItemName(
            $itemName,
            $associationTypeCode
        );
        $arrayForForm = 
            Pepit_Model_Doctrine2::getArrayForFormFromEntityWithSingleValueFields(
                $this->getEntityManagerMock(
                    $associationTypeCode,
                    $itemName
                ),
                $entityMock,
                array($formItemName)
            );
        
        $target = array(
            $formItemName => $targetValue
        );
        
        $this->assertEquals($target,$arrayForForm);
        
    }
    
    public function dataProviderItemTypeCaseOneToXField()
    {
        return array(
            array(
                'OneToMany',
                array('value1','value2','value3'),
                'value1,value2,value3',
            ),
            array(
                'OneToOne',
                'value1',
                'value1',
           )
        );
    }
    
    public function testGetArrayPropertiesforFormFromEntityMapField()
    {
        $itemName = 'string';
        $value = 'value';
        $formItemName = Backend_Model_Items::getFormItemName(
            $itemName,
            '0'
        );
        $propertySet = array(
            $formItemName 
        );
        
        $key = $propertySet[0];
        $entityMock = new stdClass();
        $entityMock->$itemName = $value;
        
        $arrayForForm = 
            Pepit_Model_Doctrine2::getArrayForFormFromEntityWithSingleValueFields(
                $this->getEntityManagerMock('0', $itemName),
                $entityMock,
                $propertySet
            );
        
        $this->assertTrue(array_key_exists($formItemName,$arrayForForm));
        $this->assertEquals($propertySet,array_keys($arrayForForm));
        $this->assertEquals($entityMock->$key,$arrayForForm[$key]);
    }
    
   
}
