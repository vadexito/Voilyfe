<?php

/**
 *
 * Categories_Model_Trees
 * 
 * @package Mylife
 * @author DM 
 */
class Categories_Model_Trees
{
    protected $_storage;
    
    protected $_em;
    
    public function __construct()
    {
        $this->_em = Zend_Registry::get('entitymanager');
        $this->_storage = $this->_em->getRepository('ZC\Entity\Category') ;
    }
    
    public function getStorage()
    {
        return $this->_storage;
    }
    
    public function getCategoryTree()
    {
        $firstAncestors = $this->findFirstAncestors($this->getStorage()->findAll());
        
        $finalTree = new Zend_Navigation();
        foreach($firstAncestors as $ancestor)
        {
            $finalTree->addPage($this->toTree($ancestor));
        }
        
        return $finalTree;
    }
    
    public function hasChildren($category)
    {
        return ($category->categories->count()>0);
    }
    
    public function getChildren($category)
    {
        return $category->categories;
    }
    
    public function toTree($category)
    {
        //leaf        
        if (!$this->hasChildren($category))
        {
            return new Zend_Navigation_Page_Uri(array(
                'uri' => '/',
                'id' => $category->id,
                'label' => $category->name
            ));
        }
        
        $containerChildren = array();
        foreach ($this->getChildren($category) as $subcategory)
        {
            $containerChildren[] = $this->toTree($subcategory);
        }
        return new Zend_Navigation_Page_Uri(array(
                'uri' => '/',
                'id' => $category->id,
                'label' => $category->name,
                'pages' => $containerChildren
            ));
    }
    
    
    
    public function findFirstAncestors($categories)
    {
        foreach ($categories as $category)
        {
            $children = $this->getChildren($category);
            foreach ($children as $child)
            {
                $categories = $this->unsetInCategories($child,$categories);
            }
                
        }
        return $categories;
    }
    
    public function unsetInCategories($child,$categories)
    {
        foreach ($categories as $key => $category)
        {
            if ($category->name === $child->name)
            {
                unset($categories[$key]);
            }
        }
        return $categories;
    }
    

}

