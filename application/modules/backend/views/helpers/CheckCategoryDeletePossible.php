<?php

class Backend_View_Helper_CheckCategoryDeletePossible extends Zend_View_Helper_Abstract
{
    
    protected $_categoriesWithEvents = NULL;
    
    const ERROR_MSG = 'A category cannot be deleted if it contains events or rows.';
    
    
    public function checkCategoryDeletePossible($categoryId,
                                                \Doctrine\ORM\EntityManager $em)
    {
        $this->setCategoriesWithEvents($em);
        return !in_array($categoryId,$this->getCategoriesWithEvents($em));
    }
    
    public function getCategoriesWithEvents($em)
    {
        if (!$this->_categoriesWithEvents)
        {
            $this->setCategoriesWithEvents($em);
        }
        return $this->_categoriesWithEvents;
    }
    
    public function setCategoriesWithEvents(\Doctrine\ORM\EntityManager $em)
    {
        if (!$this->_categoriesWithEvents)
        {
            $categories = array();
            $events = $em
                    ->getRepository('ZC\Entity\Event')
                    ->findAll();
            foreach ($events as $event)
            {
                $categories[] = $event->category->id; 
                $categories = array_unique($categories);
            }
            $this->_categoriesWithEvents = $categories;
        }
    }
}
