<?php

class Backend_InitController extends Zend_Controller_Action
{

    protected $_model;
    
    public function init()
    {
        $this->_model = new Backend_Model_Init();
    }
    
    public function indexAction()
    {  
        $this->view->dataBases = $this->_model->fetchAllTables();
    }
    
    public function initdatabaseAction()
    {
        $this->_model->initTables();
        
        $this->getHelper('flashMessenger')->addMessage(
                'Tables successfully initialized in the data base'
        );
        
        $this->_redirect($this->view->url(array(
            'controller'    => 'init',
            'action'        => 'index',
        ),'backend'));
    }
    
    public function inittranslationfilesAction()
    {
        $this->_model->initTranslationXMLFiles();
        
        $this->getHelper('flashMessenger')->addMessage(
                'XML files successfully created'
        );
        
        $this->_redirect($this->view->url(array(
            'controller'    => 'init',
            'action'        => 'index',
        ),'backend'));
    }
    
    public function googleplacesAction()
    {
        $form = new Backend_Form_GooglePlaces();
        $model = new Backend_Model_GoogleAPI();
        
        $form->setAction($this->view->url(
                array('controller' => 'init','action'=>'googleplaces'),
                'backend'
        ));
        
        // if the form or subform has been sent
        if ($this->getRequest()->isPost())
        {
            // get row from post
            $userInput = $this->getRequest()->getPost();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit')->isChecked();
            
            //check if data is valid
            if ($submitChecked && $valid)
            {    
                // insert new event
                $model->createCorrespondances($form->getValues());
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage('Correspondances successfully updated/created');

                //redirection to the same page for more inserting
                return $this->_redirect($this->view->url(
                        array(
                            'controller'    => 'init',
                            'action'        => 'index',
                        ),
                        'backend'
                ));
            }
            else
            {
                //transmit data to form
                $form->populate($form->getValues());
            }
        }
        //transmit to view the data
        $this->view->form = $form;
    }
    
    public function initgoogletypesAction()
    {
        $model = new Backend_Model_GoogleAPI();
        $model->initGoogleTypes();
        
        $this->getHelper('flashMessenger')->addMessage(
                'Google Types successfully created/updated'
        );
        
        $this->_redirect($this->view->url(array(
            'controller'    => 'init',
            'action'        => 'index',
        ),'backend'));
    }
    
    
    
    
}
