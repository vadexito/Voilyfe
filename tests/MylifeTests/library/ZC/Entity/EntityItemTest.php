<?php
/**
 *
 * @group Entities 
 */
class EntityItemTest extends Pepit_Test_DoctrineTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        
        TestHelpersDoctrine::initBaseFormItems($this->em);
    }
    
    public function testCanCreateItem()
    {
        
        $properties = array(
            'name'                  => 'newitem',
            'formLabel'             => 'new item:',
            'formRequired'          => true,
            'nullableSQL'           => true,
            'typeSQL'               => 'associationField',
            'sizeSQL'               => null,
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
        );
        
        $item =  TestHelpersDoctrine::getEntity($this->em,'\ZC\Entity\Item',$properties);
        $this->assertInstanceOf('ZC\Entity\Item', $item);  
        
        $multioption1 = TestHelpersDoctrine::getItemRow('value1',$item);
        $multioption2 = TestHelpersDoctrine::getItemRow('value2',$item);
           
        $item->addItemRow($multioption1);
        $item->addItemRow($multioption2);
        
        $classForm =  $this->em->getRepository('ZC\Entity\FormElementClass')
                ->findOneByName('Pepit_Form_Element_Select');
        $classFormName = $classForm->name;
        $item->formElementClass = $classForm;
        
        $filter1 =  $this->em->getRepository('ZC\Entity\FormFilter')->find(1);
        $filter2 =  $this->em->getRepository('ZC\Entity\FormFilter')->find(2);
        $validator1 = $this->em->getRepository('ZC\Entity\FormValidator')->find(1);
        $validator2 =  $this->em->getRepository('ZC\Entity\FormValidator')->find(2);
        $item->addFormFilter($filter1);
        $item->addFormFilter($filter2);
        $item->addFormValidator($validator1);
        $item->addFormValidator($validator2);
        
        $this->em->persist($item);        
        $this->em->flush();
        $this->em->clear();
        
        //check item
        $itemsDB = $this->em->getRepository('ZC\Entity\Item')->findAll();
        $this->assertEquals(1,count($itemsDB));
        
        $itemDB = $itemsDB[0];
        
        $this->assertEquals($properties['name'],$itemDB->name);
        $this->assertEquals($properties['formLabel'],$itemDB->formLabel);
        $this->assertEquals($properties['formRequired'],$itemDB->formRequired);
        $this->assertEquals($properties['nullableSQL'],$itemDB->nullableSQL);
        $this->assertEquals($properties['typeSQL'],$itemDB->typeSQL);
        $this->assertEquals($properties['sizeSQL'],$itemDB->sizeSQL);
        $this->assertEquals($properties['associationType'],$itemDB->associationType);
        $this->assertEquals($classFormName,$itemDB->formElementClass->name);
        $this->assertEquals(2,$itemDB->itemRows->count());        
        $this->assertEquals($multioption1->value,$itemDB->itemRows[0]->value);
        $this->assertEquals($multioption2->value,$itemDB->itemRows[1]->value);        
        $this->assertEquals(2,$itemDB->formValidators->count());        
        $this->assertEquals($validator1->name,$itemDB->formValidators[0]->name);
        $this->assertEquals($validator2->name,$itemDB->formValidators[1]->name);        
        $this->assertEquals(2,$itemDB->formFilters->count());        
        $this->assertEquals($filter1->name,$itemDB->formFilters[0]->name);
        $this->assertEquals($filter2->name,$itemDB->formFilters[1]->name);  
    }
    
    
    
     public function testCanRemoveStringItem()
    {
         $properties = array(
            'name'                  => 'new item',
            'formLabel'             => 'new item:',
            'formRequired'          => true,
            'nullableSQL'           => true,
            'typeSQL'               => 'associationField',
            'sizeSQL'               => null,
            'associationType'       => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
        );
        
        $item =  TestHelpersDoctrine::getEntity($this->em,'\ZC\Entity\Item',$properties);
        
        $itemRow = TestHelpersDoctrine::getItemRow('value1',$item);
        $item->addItemRow($itemRow);
        
        
        $item->formElementClass = 
            $this->em->getRepository('ZC\Entity\FormElementClass')->find(6);
        
        $item->addFormFilter($this->em->getRepository('ZC\Entity\FormFilter')->find(1));
        $item->addFormValidator($this->em->getRepository('ZC\Entity\FormValidator')->find(1));
        
        $this->em->persist($item);        
        $this->em->flush();
        $itemId = $item->id;
        
        $this->em->remove($item);
        $this->em->flush();
        
        //check item removed
        $itemDB = $this->em->getRepository('\ZC\Entity\Item')->find($itemId);
        $this->assertNull($itemDB);
    }
}
