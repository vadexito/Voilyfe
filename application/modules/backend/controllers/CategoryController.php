<?php

class Backend_CategoryController extends Pepit_Controller_Abstract
{

    protected $_generalizedItemType;
    protected $_generalizedItemRowType;
    
    public function init()
    {
        $this->_model = Pepit_Model_Doctrine2::loadModel('categories');
        $this->_generalizedItemType = 'category';
        $this->_generalizedItemRowType = 'event';
        
        
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
        $form->setAction($this->view->url(
            array(
                'controller'    => 'category',
                'action'        => 'create',
            ),
            'backend')
        );
        
        $this->processInsert(
            $form, 
            'category created successfully.',
            $this->view->url(
                    array(
                        'controller'    => 'category',
                        'action'        => 'index',
                    ),
                    'backend'
        ));
        
    }
    
    
    
   public function editAction()
    { 
        
        //get categoryId
        $categoryId = $this->getRequest()->getParam('entityId');
        
        //load formular
        $form = $this->_model->getForm(
            'update'
        );
        
        // set form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'category',
                'action' => 'edit',
                'entityId' => $categoryId,
            ),
            'backend'
        ));
        
        $this->processEdit(
                $form,
                'category updated successfully.',
                $this->view->url(
                    array(
                        'controller'    => 'category',
                        'action'        => 'index',
                    ),
                    'backend'),
                $categoryId
        );
    }
    
    public function deleteAction()
    { 
         //get the type of the category to be deleted
        $categoryId = $this->getRequest()->getParam('entityId');
        
        //prepare form for category creation
        $form = $this->_model->getForm('delete');
        $form->setAction($this->view->url(array(
                'controller'    => 'category',
                'action'        => 'delete',
                'entityId'      => $categoryId,
            ),
            'backend'
        )); 
        
        $this->processDelete(
                $form, 
                'Category deleted successfully.',
                $this->view->url(
                    array(
                        'controller'    => 'category',
                        'action'        => 'index',
                    ),
                    'backend'),
                $categoryId
        );
    }
}











