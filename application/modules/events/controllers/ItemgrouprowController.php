<?php

class Events_ItemgrouprowController 
    extends Events_Controller_Abstract_Abstract
{

    public function init()
    {
        parent::init();
        $this->aclResource = 'events:itemgrouprow';
        $this->_model = Pepit_Model_Doctrine2::loadModel('itemGroupRows');
        
    }
    
     
    public function indexAction()
    {  
    }

    public function createAction()
    {
        //get category of event
        $itemGroupId = $this->getRequest()->getParam('containerId');
        
        //prepare form for input event
        $form = $this->_model->getForm('insert',array(
            'containerId' => $itemGroupId,
            'containerType' => 'itemGroup',
            'model'      => $this->_model
        ));
        
        
        //define form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'itemgrouprow',
                'action' => 'create',
                'containerId'   =>$itemGroupId,
                'askComeBack'   => 'false'
            ),
            'event'
        ));
        
        $this->processInsert(
            $form, 
            'msg_itemgrouprow_created',
            $this->getHelper('lastVisited')->redirectUrl(
                $this->view->url(
                    array(
                        'controller' => 'itemgrouprow',
                        'action' => 'index',
                        'containerId' => $itemGroupId),
                    'event'
        )));
    } 
    
    public function editAction()
    {
        $itemGroupRowId = $this->getRequest()->getParam('containerRowId');
        
        $itemGroupId = $this->getRequest()->getParam('containerId');
        
        //prepare form
        $form = $this->_model->getForm('update',array(
            'containerId' => $itemGroupId,
            'containerType' => 'itemGroup',
            'model'      => $this->_model
        ));
       $form->setAction($this->view->url(
            array(
                'controller' => 'itemgrouprow',
                'action' => 'edit',
                'containerId'       => $itemGroupId,
                'containerRowId'    => $itemGroupRowId,
                'askComeBack'       => 'false'
            ),
            'event'
        ));
        
        $this->processEdit(
                $form,
                'msg_itemgrouprow_updated',
                $this->getHelper('lastVisited')->redirectUrl(
                    $this->view->url(
                        array(
                            'controller' => 'itemgrouprow',
                            'action' => 'index',
                            'containerId' => $itemGroupId),
                        'event'
                )),
                $itemGroupRowId
        );
        
        
    }

    public function deleteAction()
    {
        //get eventId of the event to be deleted
        $itemGroupRowId = $this->getRequest()->getParam('containerRowId');
        
        //get eventId of the event to be deleted
        $itemGroupId = $this->getRequest()->getParam('containerId');

        // get form
        $form = $this->_model->getForm('delete');
        
        //set form action
        //define form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'itemgrouprow',
                'action' => 'delete',
                'containerId'       => $itemGroupId,
                'containerRowId'    => $itemGroupRowId,
            ),
            
            'event'
        ));
          
         $this->processDelete(
                $form, 
                'msg_itemgrouprow_deleted',
                $this->view->url(
                        array(
                            'controller' => 'itemgrouprow',
                            'action' => 'index',
                            'containerId' => $itemGroupId),
                        'event'
                ),
                $itemGroupRowId
        );
    }
}











