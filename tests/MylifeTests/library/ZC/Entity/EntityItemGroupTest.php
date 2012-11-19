<?php
/**
 *
 * @group Entities 
 */
class EntityItemGroupTest extends Pepit_Test_DoctrineTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        
        TestHelpersDoctrine::createFormElementClasses($this->em);
    }
    
    public function testCanCreateItemGroup()
    {
        $item1 = TestHelpersDoctrine::createItemString($this->em);
        $item2 = TestHelpersDoctrine::createItemString($this->em,'second');
        $identifierItem = TestHelpersDoctrine::createItemString($this->em,'third');
        
        
        $input = array(
            'name'              => 'new item',
            'formRequired'      => true,
            'associationType'   
                                => Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE,
            'items'             => array($item1,$item2),
            'identifierItem'    => $identifierItem,
            'itemType'          => ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP,
        );
        
        $itemGroup = new ZC\Entity\ItemGroup();
        $this->assertInstanceOf('ZC\Entity\ItemGroup', $itemGroup);  
        $this->assertInstanceOf('ZC\Entity\GeneralizedItem', $itemGroup);  
        
        $itemGroup->name = $input['name'];
        $itemGroup->formRequired = $input['formRequired'];
        $itemGroup->associationType = $input['associationType'];
        $itemGroup->identifierItem = $input['identifierItem'];
        $itemGroup->itemType = $input['itemType'];
       
        $classForm =  $this->em->getRepository('ZC\Entity\FormElementClass')
                ->findOneByName('Pepit_Form_Element_Select');
        $classFormName = $classForm->name;
        $itemGroup->formElementClass = $classForm;
        
        foreach ($input['items'] as $item)
        {
             $itemGroup->addItem($item);     
        }
        
        $this->em->persist($itemGroup);        
        $this->em->flush();
        $this->em->clear();
        
        //check item
        $itemgroupsDB = $this->em->getRepository('ZC\Entity\ItemGroup')->findAll();
        $this->assertEquals(1,count($itemgroupsDB));
        
        $itemgroupDB = $itemgroupsDB[0];
        
        //check identifier item
        $this->assertEquals(
                $input['identifierItem']->id,
                $itemgroupDB->identifierItem->id);
        unset($input['identifierItem']);
        
        //check class for name
        $this->assertEquals($classFormName,$itemgroupDB->formElementClass->name);
        unset($input['formElementClass']);
        
        //check items
        $this->assertEquals(
            count($input['items']),
            $itemgroupDB->items->count()
        );  
        foreach($itemgroupDB->items as $key => $item)
        {
            $this->assertEquals($input['items'][$key]->id,$item->id);
            $this->assertEquals($input['items'][$key]->name,$item->name);
        }
        unset($input['items']);
        
        foreach ($input as $property => $value)
        {
            $this->assertEquals($value,$itemgroupDB->$property);
        }
    }
}
