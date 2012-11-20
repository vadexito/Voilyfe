<?php

class Backend_View_Helper_CheckItemDeletePossible extends Zend_View_Helper_Abstract
{
    
    protected $_itemsInCategories = NULL;
    
    const ERROR_MSG = 'Item cannot be deleted if a category exists using it.';
    
    public function checkItemDeletePossible($categories,$itemId)
    {
        $this->setItemsInCategories($categories);
        if ($this->getItemsInCategories() === NULL)
        {
            return true;
        }
        return !in_array($itemId,$this->getItemsInCategories());
    }
    
    public function getItemsInCategories($categories = NULL)
    {
        if (!$this->_itemsInCategories && $categories)
        {
            $this->setItemsInCategories($categories);
        }
        return $this->_itemsInCategories;
    }
    
    public function setItemsInCategories($categories)
    {
        if (!$this->_itemsInCategories && $categories)
        {
            $items = array();
            
            foreach ($categories as $category)
            {
                foreach ($category->items as $item)
                {
                    $items[] = $item->id; 
                    $items = array_unique($items);
                }
            }
            $this->_itemsInCategories = $items;
        }
    }
}
