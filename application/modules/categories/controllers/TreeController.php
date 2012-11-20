<?php

class Categories_TreeController extends Zend_Controller_Action
{
    protected $_model;
    
    public function init()
    {
        parent::init();
        
        $this->_model = new Categories_Model_Trees();
        
    }
    
    public function indexAction()
    {   
        $page = new Zend_Navigation_Page_Uri(array(
                'uri' => '/',
                'id' => 'bonjour',
                'label' => 'test'
        ));
        $tree = new zend_navigation(array($page));
        $helper = new Zend_View_Helper_Navigation_Menu();
        $helper->setView($this->view);
        
        echo $helper->menu()->render($this->_model->getCategoryTree());
        
       
        
        
        
    } 
}

