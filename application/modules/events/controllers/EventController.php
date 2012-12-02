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
        
        $this->_initIconsCss();
    }
     
    public function indexAction()
    {
        //get events for one day
        $date = null;
        if ($this->getRequest()->getParam('date'))
        {
            //add date in form yyyy-mm-dd
            $date = new \DateTime($this->getRequest()->getParam('date')); 
            
        }
        
        //case for all categories
        if ($this->_category->name === 'all')
        {
            $memberId = Zend_Auth::getInstance()->getIdentity()->id;
            $events = $this->_model->findEventsByMemberIdOrderByDateDesc($memberId);
            $this->view->allOption = 'all';
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
        $this->view->paginatorEvents = $this->_getPaginator($events);
    }

    
    
    protected function _getPaginator($events,$itemPerPage = NULL,$firstPage=1)
    {
        if ($itemPerPage === NULL)
        {
            $itemPerPage = $this->_config->events
                ->index->paginator->itemCountPerPage;
        }
        
        $paginator = Zend_Paginator::factory($events);
        $paginator->setItemCountPerPage($itemPerPage);
        
        if ($this->_getParam('page'))
        {
            $paginator->setCurrentPageNumber($this->_getParam('page'));
        }
        else 
        {
            $paginator->setCurrentPageNumber($firstPage);
        }
        
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
    
    public function showAction()
    {
        $eventId = $this->getRequest()->getParam('containerRowId');
        $allOption = $this->getRequest()->getParam('all');
        
        $events = $this->_model->findEventsByCategoryByMemberIdOrderByDateDesc(
            $this->_userId,
            $this->_category
        );
        
        foreach ($events as $key => $event)
        {
            if ($event->id == $eventId)
            {
                $page = $key+1;
            }
        }
        
        $paginator = $this->_getPaginator($events,1,$page);
        
        $this->view->allOption = $allOption;
        $this->view->paginatorEvents = $paginator;
        $this->view->eventBefore = '';
        $this->view->eventAfter = '';
    }
    
    protected function _initIconsCss()
    {
        $dir = APPLICATION_PATH.'/../public/images/icons/nav_bar/';
        $this->view->headStyle()->captureStart();
        $pattern = '#^([a-zA-Z0-9\-_]*).png$#';
        foreach (scandir($dir) as $icon)
        {
            if (preg_match($pattern,$icon))
            {
                $icon = preg_replace($pattern,'$1',$icon);
                echo '.ui-icon-mylife-'.$icon.'{'
                .'background:url("/images/icons/nav_bar/'.$icon.'.png") 50% 50% no-repeat;'
                .'background-size: 24px 22px;}'."\n";
            }
            
        }
        $this->view->headStyle()->captureEnd();
    }
   
}











