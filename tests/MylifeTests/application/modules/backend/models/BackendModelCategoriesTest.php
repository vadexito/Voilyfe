<?php

/**
 * @group Models 
 * @group Backend
 */


class BackendModelCategoriesTest extends Pepit_Test_DoctrineTestCase
{
    protected $_repository;
    
    protected $_model;
    
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_repository = $this->em->getRepository('ZC\Entity\Category');
        
        $this->_model = new Backend_Model_Categories();
        
        
    }
    
    public function testCreateEntityNewCategory()
    {
        $item1 = TestHelpersDoctrine::createItemOneToMany($this->em);
        $item2 = TestHelpersDoctrine::createItemManyToOne($this->em);
        $item3 = TestHelpersDoctrine::createItemString($this->em);
        
        $formData = array(
            'name'                  => 'newCategory',
            'itemIds'                 => array(1,2,3)
        );
        
        //insert new $user
        $category = $this->_model->createEntityFromForm($formData);
        $this->em->persist($category);
        $this->em->flush();
        $this->em->clear();
        
        $categoryDB = $this->_repository->findOneByName($formData['name']);
        
        $this->assertInstanceOf('\ZC\Entity\Category',$categoryDB);
        $this->assertEquals($formData['name'],$categoryDB->name);
        $this->assertEquals($item1->name,$categoryDB->items[0]->name);
        $this->assertEquals($item2->name,$categoryDB->items[1]->name);
        $this->assertEquals($item3->name,$categoryDB->items[2]->name);
        
    }
}
