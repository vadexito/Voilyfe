<?php

class Pepit_Controller_Abstract extends Pepit_Controller_Abstract_Abstract
{
    protected $_model;
    
    public function processInsert($form,$messageSuccess,$redirectUrl)
    {
        // if the form or subform has been sent
        if ($this->getRequest()->isPost())
        {
            // get row from post
            $userInput = $this->getRequest()->getPost();
            
            $valid = $form->isValid($userInput);
            
            $submitChecked = $form->getElement('submit_insert')->isChecked();
            //var_dump($form->getErrors());
            //var_dump($userInput);
            //var_dump($form->getValues());die;
            
            
            
            //check if data is valid
            if ($submitChecked && $valid)
            {    
                // insert new event
                $this->_model->insert();
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage($messageSuccess);

                //redirection to the same page for more inserting
                return $this->_redirect($redirectUrl);
                
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
    
    public function processEdit(
        $form,$messageSuccess,$redirectUrl,$entityId)
    {
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
                // modify entity
                $this->_model->update($form->getValues(),$entityId);
                
                // set saved message
                $this->getHelper('flashMessenger')->addMessage($messageSuccess);
                
                //redirection to the index page
                return $this->_redirect($redirectUrl);
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
                    $this->_model->getArrayForFormUpdateFromEntity($entityId)
            );
        }
        
        
        //transmit to view the data
        $this->view->form = $form;
    }
    
    public function processDelete(
            $form,$messageSuccess,$redirectUrl,$entityId)
    {
        // check if form was sent
        if ($this->getRequest()->isPost() || $this->getRequest()->isGet() )
        {
            // get row from post
            $userInput = $this->getRequest()->getParams();
            $valid = $form->isValid($userInput);
            $submitChecked = $form->getElement('submit_delete')->isChecked();
            
            // check if data are ok
            if ($valid && $submitChecked) 
            {
                // delete event
                $this->_model->delete($entityId);
                
                // send message
                $this->getHelper('flashMessenger')->addMessage($messageSuccess);
                
                // redirect to list of event
                return $this->_redirect($redirectUrl);
            }
        }
        // transmit data to view
        $this->view->form  = $form;
    }
}

