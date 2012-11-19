<?php
/**
 *
 * @group Entities 
 */
class EntityCategoryTest extends Pepit_Test_DoctrineTestCase
{
    protected $properties;
    
    public function setUp()
    {
        $this->properties = array(
            'name'      => 'testCategory'
        );
        parent::setUp();
    }
    
    public function testCanCreateCategoryWithNoItem()
    {
        $category =  TestHelpersDoctrine::getEntity(
                $this->em,
                '\ZC\Entity\Category',
                $this->properties,
                true,true
        );
        $categoryId = $category->id;
        $this->assertInstanceOf('ZC\Entity\Category',$category);
        $this->em->clear();
        
        $categoryDB = $this ->em
                            ->getRepository('ZC\Entity\Category')
                            ->find($categoryId);
        $this->assertEquals($category->id,$categoryDB->id);
        $this->assertEquals($category->name,$categoryDB->name);
        if ($category->items)
        {
            $this->assertEquals(
            $category->items->count(),
            $categoryDB->items->count()
            );
            $itemsDB = $categoryDB->items;
            foreach ($category->items as $key => $item)
            {
                $this->assertEquals($item->id,$itemsDB[$key]->id);
            }
        }
    }
    
    public function testCanCreateCategoryWithItems()
    {
        TestHelpersDoctrine::initBaseFormItems($this->em);
        
        //create category and add item
        $category =  TestHelpersDoctrine::getEntity(
            $this->em,
            '\ZC\Entity\Category',
            $this->properties
        );
        $string = TestHelpersDoctrine::createItemString($this->em);
        $category->addItem($string);          
        $category->addItem(TestHelpersDoctrine::createItemOneToMany($this->em));          
        $category->addItem(TestHelpersDoctrine::createItemManyToOne($this->em));  
        
        $this->em->persist($category);
        $this->em->flush();
        $this->em->clear();
        
        
       
        $categoryDB = $this->em->getRepository('ZC\Entity\Category')
                ->find($category->id);
        
        //check items attached to category
        if ($category->items)
        {
            $this->assertEquals(
            $category->items->count(),
            $categoryDB->items->count()
            );
            $itemsDB = $categoryDB->items;
            foreach ($category->items as $key => $item)
            {
                $this->assertEquals($item->id,$itemsDB[$key]->id);
            }
        }
    }
    
}
