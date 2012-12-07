<?php

class Members_SettingsController extends Pepit_Controller_Abstract_Abstract
{

    protected $_member;
    protected $_model;
    
    public function init()
    {
        $this->_member = Zend_Auth::getInstance()->getIdentity();
        $this->_model = new Members_Model_Settings();
        
    }

    public function indexAction()
    {
        $form = $this->_model->getForm('update');
        $form->setAction($this->view->url([
            'module' => 'members',
            'controller' => 'settings'
        ]));
        
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
                $this->_model->update();
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage(
                        $this->view->translate('msg_user_preferences_saved')
                );
                
                //redirection to the login page
                $this->_redirect($this->view->url([],'home'));
            }
            else
            {
                //transmit data to form
                $form->populate($userInput);
            }
        }
        //transmit to view the data
        $this->view->form = $form;
    
        
    }  
       
    public function setpreferencesAction()
    {
        
    }  
       
}