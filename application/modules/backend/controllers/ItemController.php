<?php

class Backend_ItemController extends Pepit_Controller_Abstract
{

    protected $_generalizedItemType;
    protected $_generalizedItemRowType;
    
    public function init()
    {
        // init properties
        $this->_model = Pepit_Model_Doctrine2::loadModel('items');
        $this->_generalizedItemType = 'item';
        $this->_generalizedItemRowType = 'itemRow';
        
         //init viewrenderer
        $this->view->setScriptPath(APPLICATION_PATH . '/modules/backend/views/scripts/generalized-item/');
        $this->_helper->viewRenderer->setRender(null,null,true);
        
    }
     
    public function indexAction()
    {
        $this->view->generalizedItems = $this->_model->fetchEntries();
        $this->view->generalizedItemType = $this->_generalizedItemType;
        $this->view->generalizedItemRowType = $this->_generalizedItemRowType;
        $this->view->generalizedItemRowsCounts = 
                $this   ->_model
                        ->getEntityManager()
                        ->getRepository(
                                'ZC\Entity\\'.ucfirst($this->_generalizedItemRowType)
                        )
                        ->findCountsByContainerName($this->_generalizedItemType);
    }

    /**
     * create a new item
     * 
     *  
     */
    public function createAction()
    { 
        //prepare form for category creation
        $form = $this->_model->getForm(
                'insert',
                array(
                    'entitymanager' => Zend_Registry::get('entitymanager')
                ));
        $form->setAction($this->view->url(array(
                'controller'    => 'item',
                'action'        => 'create',
            ),'backend'));
        
        $this->processInsert(
            $form, 
            'Item created successfully.',
            $this->view->url(array(
                'controller' => 'item'
            ),'backend')
        );
    }
   
    
    public function editAction()
    { 
        $itemId = $this->getRequest()->getParam('entityId');
        
        $form = $this->_model->getForm(
                'update',
                array(
                    'entitymanager'     => Zend_Registry::get('entitymanager')
                ));
        // set form action
        $form->setAction($this->view->url(
            array(
                'controller'    => 'item',
                'action'        => 'edit',
                'entityId'      => $itemId,
            ),
            'backend'
        ));
        
        $this->processEdit(
                $form,
                'Item updated successfully.',
                $this->view->url(array(
                    'controller' => 'item',
                    'action'     => 'index'
                ),'backend'),
                $itemId
        );
    }
    
    
    public function deleteAction()
    { 
         //get the type of the category to be deleted
        $itemId = $this->getRequest()->getParam('entityId');
        
        //prepare form for category creation
        $form = $this->_model->getForm('delete');
        $form->setAction($this->view->url(array(
                'controller'    => 'item',
                'action'        => 'delete',
                'entityId'      => $itemId,
            ),
            'backend'
        ));        
        
        $this->processDelete(
                $form, 
                'Item deleted successfully.',
                $this->view->url(array(
                    'controller'    => 'item',
                    'action'        => 'index' 
                ),'backend'),
                $itemId
        );
    }
    
}











