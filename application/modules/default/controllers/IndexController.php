<?php

class IndexController extends Pepit_Controller_Abstract_Abstract
{
    
    public function init()
    {
        parent::init();
        
    }
    
    public function indexAction()
    {   
        $model = new Events_Model_Events();
        
        $nbMaxMetaCategoryWidgets=2;
        $nbMaxSingleCategoryWidgets=4;
        
        $widgets = array_merge(
                 $model->getWidgetsForBestMetaCategories(
                    $nbMaxMetaCategoryWidgets,
                    Zend_Auth::getInstance()->getIdentity()->id
                ),
                $model->getWidgetsForBestCategories(
                    $nbMaxSingleCategoryWidgets,
                    Zend_Auth::getInstance()->getIdentity()->id
        ));
        
       $this->view->widgets = $widgets;
    } 
}

