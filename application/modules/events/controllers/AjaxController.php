<?php

class Events_AjaxController extends Zend_Controller_Action
{
    use Events_Controllers_Trait;
    
    protected $_model;
    protected $_category;
    
    public function init()
    { 
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContexts([
            'widgetchart' => 'json',
            'eventcalendar' => 'json',
            'datelocale' => 'json',
            'validateform' => 'json',
            'knownlocations' => 'json',
            'googlelocations' => 'json',
        ])->initContext();
        
        $this->_model = new Events_Model_Events();
        
        $this->_category = $this->getCategory(
                $this->_model, 
                $this->getRequest()->getParam('containerId')
        );
        
    }
    
    public function widgetchartAction()
    {
        $optionWidget = [];
        if ($this->getRequest()->getParam('parameter'))
        {
            $parameter = $this->getRequest()->getParam('parameter');
            $optionWidget['parameter'] = $parameter;
        }
        
        
        if (is_numeric($this->_category))
        {
            $events = $this->_model->findEventsByCategoryByMemberIdOrderByDateDesc(
                Zend_Auth::getInstance()->getIdentity()->id,
                $this->_category                
            );
            $nameForTranslation = 'category_'.$this->_category->name;
            $categoryName = $this->view->translate($nameForTranslation);
        }
        else
        {
            $events = $this->_model->findEventsByMemberIdOrderByDateDesc(
                    Zend_Auth::getInstance()->getIdentity()->id
            );
            $categoryName = $this->view->translate('legend_all_events');
        }
        
        $chart = new Pepit_Widget_Chart($events,['titleYAxis'=>$categoryName]);
        
        
        $this->view->dataChart = $chart->dataForGoogleCharts($optionWidget);
        
    }
    
    public function eventcalendarAction()
    {
       //get events
        if ($this->_category)
        {
            $events = $this->_model->findEventsByCategoryByMemberIdOrderByDateDesc(
                               $this->_userId,
                               $this->_category);
        }
        else
        {
            $events = $this->_model->findEventsByMemberIdOrderByDateDesc(
                    $this->_userId
            );
        }
        
        $this->view->eventDates = $this->_model->getEventPerDay($events);
        
    }
    
    /**
     * localization date for data picker jquery ui
     *  
     */
    public function datelocaleAction()
    {
        $date = (new Zend_date(
                $this->getRequest()->getParam('dateW3C'),
                Zend_Date::ISO_8601
        ));
        
        $this->view->date = $date->toString(Zend_Date::DATE_MEDIUM);
    }
    
    
    public function validateformAction()
    {
        $form = $this->_model->getForm('insert',array(
            'containerId' => $this->_category->id,
            'containerType' => 'category',
            'model'         => $this->_model
        ));
        
        $form->isValid($this->_getAllParams());
        $this->view->messages = $form->getMessages();
    }
    
    public function knownlocationsAction()
    {
        $this->view->locations = array(
            array(
                'address'   => 'bla',
                'lat'       => 2.25,
                'lgn'       => 1.25,
            ),
            array(
                'address'   => 'bli',
                'lat'       => 1.25,
                'lgn'       => -1.25,
            ),
        );
    }
    
    public function googlelocationsAction()
    {
        if ($this->getRequest()->getParam('type'))
        {
            $type = $this->getRequest()->getParam('type');
        }
        
        $model = new Backend_Model_GoogleAPI();
        
        if ($model->getCategoryIdFromType($type))
        {
            $categoryId = $model->getCategoryIdFromType($type);
            $category = $this->_model->getEntityManager()
                                 ->getRepository('ZC\Entity\Category')
                                 ->find($categoryId);
            
            $categoryName = 'category_'.$category->name;
            $this->view->locationData = array(
                    'category'              => $this->view->translate($categoryName),
                    'url_new_event'         => $this->view->url(
                        array('action' => 'create', 'containerId' => $category->id),
                        'event'
                    ),
                    'iconPath'              => sprintf(
                                                Zend_Registry::get('config')
                                                    ->public->images
                                                    ->icon->category->path,
                                                ucfirst($category->name)
            ));
        }
        else
        {
            $name = 'unknown';
            $this->view->locationData = array(
                    'category'              => $this->view->translate('category_unknown')." ('".$type."')",
                    'url_new_event'         => '#list_singleCategories',
                    'iconPath'              => sprintf(
                                                Zend_Registry::get('config')
                                                    ->public->images
                                                    ->icon->category->path,
                                                    ucfirst($name)
            ));
        }
    }  
    
   
}











