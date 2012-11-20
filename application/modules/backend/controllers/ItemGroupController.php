<?php

class Backend_ItemGroupController extends Pepit_Controller_Abstract
{

    protected $_generalizedItemType;
    protected $_generalizedItemRowType;
    
    public function init()
    {
        $this->_model = Pepit_Model_Doctrine2::loadModel('itemGroups');
        $this->_generalizedItemType = 'itemGroup';
        $this->_generalizedItemRowType = 'itemGroupRow';
        
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


    public function createAction()
    {
        //prepare form for category creation
        $form = $this->_model->getForm(
            'insert',
            array(
                'entitymanager' => Zend_Registry::get('entitymanager')
            )
        );
        $form->setAction($this->view->url(array(
                'controller'    => 'itemgroup',
                'action'        => 'create',
            ),'backend'));
        
        $this->processInsert(
            $form, 
            'Item Group created successfully.',
            $this->view->url(array(
                'controller' => 'itemgroup',
                'action'     => 'index'
            ),'backend')
        );
    }
    
   
    
   public function editAction()
    { 
        
        //get categoryId
        $itemGroupId = $this->getRequest()->getParam('entityId');
        
        //load formular
        $form = $this->_model->getForm(
            'update',
            array(
                'entitymanager' => Zend_Registry::get('entitymanager')
            )
        );
        
        // set form action
        $form->setAction($this->view->url(
            array(
                'controller'    => 'itemgroup',
                'action'        => 'edit',
                'entityId'      => $itemGroupId,
            ),
            'backend'
        ));
        
        $this->processEdit(
                $form,
                'Item Group updated successfully.',
                $this->view->url(array(
                    'controller' => 'itemgroup'
                ),'backend'),
                $itemGroupId
        );
        
    }
    
    public function deleteAction()
    { 
         //get the type of the category to be deleted
        $itemGroupId = $this->getRequest()->getParam('entityId');
        
        //prepare form for category removal
        $form = $this->_model->getForm('delete');
        $form->setAction($this->view->url(array(
            'controller'    => 'itemgroup',
                'action'        => 'delete',
                'entityId'      => $itemGroupId,
            ),
            'backend'
        ));        
        
        $this->processDelete(
                $form, 
                'Item Group deleted successfully.',
                $this->view->url(array(
                    'controller' => 'itemgroup'
                ),'backend'),
                $itemGroupId
        );
    }
    
}

















