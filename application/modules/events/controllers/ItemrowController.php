<?php

class Events_ItemrowController 
    extends Events_Controller_Abstract_Abstract
{

    public function init()
    {
        parent::init();
        $this->aclResource = 'events:itemrow';
        $this->_model = Pepit_Model_Doctrine2::loadModel('itemRows');
        
    }
    
     
    public function indexAction()
    {
    }

    public function createAction()
    {
        //get category of event
        $itemId = $this->getRequest()->getParam('containerId');
        
        //prepare form for input event
        $form = $this->_model->getForm('insert',array(
            'containerId'   => $itemId,
            'containerType' => 'item',
            'model'         => $this->_model
        ));
       
        //define form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'itemrow',
                'action' => 'create',
                'containerId'        => $itemId,
                'askComeBack'   => 'false'),
            'event'
        ));
        $this->processInsert(
            $form, 
            'msg_itemrow_saved',
            $this->getHelper('lastVisited')->redirectUrl($this->view->url(
                array(
                    'controller' => 'itemrow',
                    'action'=> 'index',
                    'containerId' => $itemId),
                'event'
        )));
        
    } 
    
    public function editAction()
    {
        $itemRowId = $this->getRequest()->getParam('containerRowId');
        
        $itemId = $this->getRequest()->getParam('containerId');
        
        //prepare form
        $form = $this->_model->getForm('update',array(
            'containerId' => $itemId,
            'containerType' => 'item',
            'model'      => $this->_model
        ));
       $form->setAction($this->view->url(
            array(
                'controller' => 'itemrow',
                'action' => 'edit',
                'containerId'        => $itemId,
                'containerRowId'     => $itemRowId,
                'askComeBack'   => 'false'
            ),
            'event'
        ));
        
        $this->processEdit(
                $form,
                'msg_itemrow_saved',
                $this->getHelper('lastVisited')->redirectUrl(array(
                            'controller' => 'itemrow',
                            'action'=> 'index',
                            'containerId' => $itemId),
                        'event'
                ),
                $itemRowId
        );
    }

    public function deleteAction()
    {
        //get eventId of the event to be deleted
        $itemRowId = $this->getRequest()->getParam('containerRowId');
        
        //get eventId of the event to be deleted
        $itemId = $this->getRequest()->getParam('containerId');

        // get form
        $form = $this->_model->getForm('delete');
        
        //set form action
        //define form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'itemrow',
                'action' => 'delete',
                'containerId'       => $itemId,
                'containerRowId'    => $itemRowId,
            ),
            
            'event'
        ));
          
         $this->processDelete(
                $form, 
                'msg_itemrow_deleted',
                $this->view->url(array(
                            'controller' => 'itemrow',
                            'action'=> 'index',
                            'containerId' => $itemId),
                        'event'
                ),
                $itemRowId
        );
    }
}











