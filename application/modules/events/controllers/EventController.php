<?php


class Events_EventController extends Events_Controller_Abstract_Abstract
{
    use Events_Controllers_Trait;
    
    protected $_category = NULL;
    
    protected $_config;
    
    
    public function init()
    {
        parent::init();
        
        //used to initialize predispatch function
        $this->aclResource = 'events:event';
        $this->_model = new Events_Model_Events();
        
        $this->_category = $this->getCategory(
                $this->_model, 
                $this->getRequest()->getParam('containerId')
        );
        
        $this->view->category = $this->_category;
        $this->_config = Zend_Registry::get('config');
        
        //initialize calendar widget for localization
        $this->_helper->calendar($this->view);
    }
     
    public function indexAction()
    {
        //get events for one day
        $date = null;
        if ($this->getRequest()->getParam('day') && 
            $this->getRequest()->getParam('month') && 
            $this->getRequest()->getParam('year'))
        {
            $day = $this->getRequest()->getParam('day');
            $month = $this->getRequest()->getParam('month');
            $year = $this->getRequest()->getParam('year');
            $date = new \DateTime($year.'-'.$month.'-'.$day);
        }
        
        //case for all categories
        if ($this->_category->name === 'all')
        {
            $memberId = Zend_Auth::getInstance()->getIdentity()->id;
            $events = $this->_model->findEventsByMemberIdOrderByDateDesc($memberId);
        }
        else
        //only one choosen category    
        {
            $events = $this->_model->findEventsByCategoryByMemberIdOrderByDateDesc(
                               $this->_userId,
                               $this->_category,
                               $date
            );
            
            //get widget graphs
            $widget = $this->_model->getWidget(
                $this->_category->id,
                Zend_Auth::getInstance()->getIdentity()->id
            );
            
            //get properties for widget char graph
            $items = $this->_category->items;
            $numberProperties = array();
            foreach($items as $item)
            {
                if (property_exists($item,'typeSQL'))
                {
                    $type = $item->typeSQL;
                    if ($type == 'integer' || $type == 'float')
                    {
                        $numberProperties[]= array(
                            'name' => Events_Model_Events::getPropertyName($this->_category->name, $item->name),
                            'itemName' =>$item->name
                        );
                    }
                }
            }
            $this->view->widget = $widget;
            $this->view->properties = $numberProperties;
        
        }
        
        $this->view->eventsPerDay = $this->_model->getEventPerDay($events);
        
        //sent to the view
        $this->view->events = $events;
        $this->view->paginatorEvents = $this->_initPaginator($events);
    }

    
    
    protected function _initPaginator($events)
    {
        $paginator = Zend_Paginator::factory($events);
        
        $paginator->setItemCountPerPage($this->_config->events
                ->index->paginator->itemCountPerPage);
        
        $paginator->setCurrentPageNumber($this->_getParam('page'),1);
        
        
        
        return $paginator;
    }
    
    
    public function createAction()
    {
        //prepare form for input event
        
        $form = $this->_model->getForm('insert',array(
            'containerId' => $this->_category->id,
            'containerType' => 'category',
            'model'         => $this->_model
        ));
        
        //define form action
        $form->setAction($this->view->url(
            array(
                'action' => 'create',
                'containerId'=>$this->_category->id),
            'event'
        ));
        $this->processInsert(
            $form, 
            $this->view->translate('msg_event_created'),
            $this->getHelper('lastVisited')->redirectUrl($this->view->url(
                array(
                    'action' => 'index',
                    'containerId' => $this->_category->id),
                'event'
            ))
        );
        
        
        
        
        
        
    } 
    
    public function editAction()
    {
        //get eventId of the event to be edited
        $eventId = $this->getRequest()->getParam('containerRowId');
        
        //load formular
        $form = $this->_model->getForm('update',array(
            'containerId' => $this->_category->id,
            'containerType' => 'category',
            'model'         => $this->_model
        ));
        
        // set form action
        $form->setAction($this->view->url(
            array(
                'action' => 'edit',
                'containerId' => $this->_category->id,
                'containerRowId'    => $eventId,
            ),
            'event'
        ));

        $this->processEdit(
                $form,
                $this->view->translate('msg_event_updated'),
                $this->getHelper('lastVisited')->redirectUrl($this->view->url(
                    array(
                        'action' => 'index',
                        'containerId' => $this->_category->id
                ),
                    'event'
                )),
                $eventId
        );
    }

    public function deleteAction()
    {
        //get eventId of the event to be deleted
        $eventId = $this->getRequest()->getParam('containerRowId');
        
        // get form
        $form = $this->_model->getForm('delete');
        
        //set form action
        $form->setAction($this->view->url(
            array(
                'action' => 'delete',
                'containerId'       => $this->_category->id,
                'containerRowId'    => $eventId,
            ),
            'event'
        ));    
        
        $this->processDelete(
                $form, 
                $this->view->translate('msg_event_deleted'),
                $this->view->url(
                    array(
                        'action' => 'index',
                        'containerId' => $this->_category->id),
                    'event'
                ),
                $eventId
        );
    }
    
    
    
    
    public function findAction()
    {
        
    }
   
}











