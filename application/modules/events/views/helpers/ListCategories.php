<?php

class Events_View_Helper_ListCategories extends Zend_View_Helper_Abstract
{
    
    public function listCategories($partialView,$namePage)
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
    
    static public function compEvents($a,$b)
    {
        return ($b['countEvent'] - $a['countEvent']);
    }
}
