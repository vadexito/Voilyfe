<?php

class Layout_View_Helper_ListCategories extends Zend_View_Helper_Abstract
{
    
    public function listCategories($partialView,$namePage)
    {   
        $mainMenu = new Zend_Navigation();
        $this->view->navigation()->menu()->setPartial($partialView);
        
        if ($this->view->navigation()->findById($namePage))
        {
            $mainMenu->addPage($this->view->navigation()->findById($namePage));
        }
        return $this->view->navigation()->menu($mainMenu);
    }
    
    static public function compEvents($a,$b)
    {
        return ($b['countEvent'] - $a['countEvent']);
    }
}
