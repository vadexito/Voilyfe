<?php

class StaticContentController extends Zend_Controller_Action
{
    public function init()
    {   
    }
    
    public function displayAction()
    {        
        $page = $this->getRequest()->getParam('page');
        $path = $this->view->getScriptPath(null)
                ."/".$this->getRequest()->getControllerName()
                ."/"."/$page.".$this->viewSuffix;
        //echo $path;die;
        if (file_exists($path))
        {
            $this->render($page);
        }
        else
        {
            throw new Zend_Controller_Exception('Page not found',404);
        }  
    }
}

    




