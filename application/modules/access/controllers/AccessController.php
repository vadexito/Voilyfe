<?php

class Access_AccessController extends Pepit_Controller_Abstract_Abstract
{

    protected $_model;
    
    
    public function init()
    {
        parent::init();
        
        //load model
        $this->_model = Pepit_Model_Abstract_Abstract::loadModel('access');
    }
   

    public function indexAction()
    {
        $formLogin = $this->_model->getForm('login');
        $formLogin->setAction($this->view->url(array(
            'action' => 'login'
        ),'access'));
        
        $formRegister = new Members_Form_UserRegister();
        $formRegister->setAction($this->view->url(array(
            'action' => 'register'
        ),'member'));
        
        
        $this->view->formLogin = $formLogin;
        $this->view->formRegister = $formRegister;
    }


    public function loginAction()
    {
        // get form
        $form = $this->_model->getForm('login');
        $form->setAction($this->view->url(array(
            'action' => 'login'
        ),'access'));
 
        if ($this->getRequest()->isPost())
        {
            $userInput = $this->getRequest()->getPost();
            $valid = $form->isValid($userInput);
                        
            $submitChecked = $form->getElement('submit_login')->isChecked();
            //check if the data is valid to be added
            if ($valid && $submitChecked)
            {
                if ($this->_model->processLogin())
                {
                     // If "rememberMe" was marked
                    if ($this->getRequest()->getPost('rememberMe',true))
                    {
                        Zend_Session::rememberMe();                               
                    }
                    else
                    {
                        Zend_Session::forgetMe();
                    }
            
                    return $this->_redirect($this->view->url([],'home'));
                }
                else
                {
                    //sending to the user a message or error
                    $form->getElement('userPassword')->addError('msg_wrong_password_or_username');
                }
            }
            $form->populate($userInput);
        }
        $this->view->formLogin = $form;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->getHelper('Redirector')->gotoRoute(array(
            'action' => 'index'
        ),'access');
    }
    
    public function getpasswordAction()
    {
        //@ML-TODO forgotten password
    }
}







