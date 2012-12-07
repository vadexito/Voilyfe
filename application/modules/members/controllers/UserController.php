<?php

class Members_UserController extends Pepit_Controller_Abstract_Abstract
{

    protected $_model;
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $resource = new Application_Acl_WithOwnerResource(
                'members:user',
                Zend_Auth::getInstance()->getIdentity()->id 
        );
        $this->_helper->aclAuthor($resource);
        
    }
    
    public function init()
    {
        //load model
        $this->_model = new Members_Model_Users;
    }

    public function registerAction()
    {
        
        //load formular
        $form = $this->_model->getForm('insert',null,'members');
        $form->setAction($this->view->url(array(
            'controller'    => 'user',
            'action'        => 'register'
        ),'member'));
                
        // if the form has been fullfilled
        if ($this->getRequest()->isPost())
        {
            // get row from post
            $userInput = $this->getRequest()->getPost();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit_register')->isChecked();

            //check if data is valid
            if ($valid && $submitChecked)
            {    
                // modify user profile
                $this->_model->insert($form->getValues());
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage(
                        $this->view->translate('msg_user_registered')
                );
                
                //redirection to the login page
                $this->_redirect($this->view->url([
                    'action' => 'login'
                ],'access'));
            }
            else
            {
                //transmit data to form
                $form->populate($userInput);
            }
        }
        //transmit to view the data
        $this->view->formRegister = $form;
    }  
       
    /**
     * modify user profile
     * 
     *  
     */
    
    public function editAction()
    {
        // get if of user to be edited
        $memberId = ($this->getRequest()->getParam('memberId'));
        
        $form = $this->_model->getForm('update');
        
        // set form action
        $form->setAction($this->view->url(
                array(
                    'controller' => 'user',
                    'action' => 'edit',
                    'memberId' => $memberId),
                'member'
        ));
        
        // if the form has been fullfilled
        if ($this->getRequest()->isPost())
        {
            // get row from post
            $userInput = $this->getRequest()->getPost();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit_update')->isChecked();

            //check if data is valid
            if ($valid && $submitChecked)
            {
                // modify user profile
                $this->_model->update($memberId);
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage('msg_user_updated');
                
                //redirection to the index page
                return $this->_redirect($this->view->url([],'home'));
            }
            else
            {
                $form->populate($userInput);
            }
        }
        else
        {
            //fullfill form with existing data the first time 
            $form->populate(
                    $this->_model->getArrayForFormUpdateFromEntity($memberId)
            );
        }
        
        //transmit to view the data
        $this->view->form = $form;
    }
    
    
    public function deleteAction()
    {
        //get userId of the user to be deleted
        $memberId = $this->getRequest()->getParam('memberId');
        
        // get delete form
        $form = $this->_model->getForm('delete');
        
        //set form action
        $form->setAction($this->view->url(
            array(
                'controller' => 'user',
                'action' => 'delete',
                'memberId' => $memberId),
            'member'
        ));
        
        // check if form was sent
        if ($this->getRequest()->isPost())
        {
            // get row from post
            $userInput = $this->getRequest()->getPost();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit_delete')->isChecked();

            //check if data is valid
            if ($valid && $submitChecked)
            {                
                // delete event
                $this->_model->delete($memberId);
                
                // send message
                $this->getHelper('flashMessenger')->addMessage('msg_user_deleted');
                
                // redirect to user home page
                return $this->_redirect($this->view->url(array(),'home'));
            }
        }
        
        // Give form to the view
        $this->view->form  = $form;
    }
    
    
    
}







