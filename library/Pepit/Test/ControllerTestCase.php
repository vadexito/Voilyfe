<?php

include_once 'Zend/Test/PHPUnit/ControllerTestCase.php' ;

abstract class Pepit_Test_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    
    protected $application;
    
    protected $vr;
    
    protected $em;

    protected function setUp()
    {
        $this->bootstrap = array($this,'appBootstrap');
        parent::setUp();
        
        $this->em = Zend_Registry::get('entitymanager');
        
        $this->vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        
    }
    
    public function tearDown()
    {
        $this->resetRequest();
        $this->resetResponse();
        parent::tearDown();
        
    }
            
    
    public function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        
        $this->application->bootstrap();
        
        $bootstrap = $this->application->getBootstrap();

        $bootstrap  ->getResource('FrontController')
                    ->setParam('bootstrap', $bootstrap);
    }
    
    
    /**
     * Simulate login of member
     */
    public function loginUser($user,$password)
    {
        // Prepare data for login
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'userName'          => $user,
            'userPassword'      => $password,
            'submit_login'      => $this->vr->view->translate('action_login')
        ));
        
        // execute login
        $this->dispatch('/user/login');

        // empty variables after login
        $this->resetRequest();
        $this->resetResponse();
        $this->request->setPost(array());
    }
    
    /**
     * User logout
     */
    public function logoutUser()
    {
        // execute logout
        $this->dispatch('/user/logout');

        // empty variables after logout
        $this->resetRequest();
        $this->resetResponse();
        $this->request->setPost(array());
    }
    
}