<?php

class Application_View_Helper_ListCategories extends Zend_View_Helper_Abstract
{
    //comparison function for ordering the categories in function of the number 
    // of events in the category
    public $compEvents = NULL;
    
    public function listCategories($partialView = NULL,$namePage = NULL)
    {   
        if ($partialView & $namePage)
        {
            if ($this->view->navigation()->findById($namePage))
            {
                $this->view->navigation()->menu()->setPartial($partialView);
                $mainMenu = new Zend_Navigation();
                $mainMenu->addPage($this->view->navigation()->findById($namePage));
                return $this->view->navigation()->menu($mainMenu);
            }
            else
            {
                return '';
            }
        }
        else
        {
           $this->initCompEvents();
           return $this;
        }
        
    }
    
    public function initCompEvents()
    {
        if (!$this->compEvents)
        {
            $this->compEvents = function ($a,$b)
            {
                return ($b['countEvent'] - $a['countEvent']);
            };
        }
    }
    
    
}
