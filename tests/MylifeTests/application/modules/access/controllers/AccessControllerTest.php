<?php

/**
 * @group Controllers
 * @group Access
 */

class AccessControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    public function setUp()
    {
        parent::setUp();
        
        $userName = "userName";
        $this->memberPass = 'pass'; 
        $this->member =        
        $member = TestHelpersDoctrine::getMember(
            $this->em,
            $userName,
            $this->memberPass,true,true
        );
    }


    public function testUserAccess()
    {
        
        // dispatch
        $this->dispatch($this->url(array(
            'action' => 'index'
        ),'access'));
        
        $this->assertController('access');
        $this->assertAction('index');
    }
    
    public function testUserLoginSuccessAndRememberMe()
    {
        // Prepare data for login
        
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'userName'      => $this->member->userName,
            'userPassword'  => $this->memberPass,
            'rememberMe'    => 1,
            'submit_login'  => $this->vr->view->translate('action_login')
        ));

        // execute login
        $this->dispatch($this->url(array(
            'action' => 'login'
        ),'access'));
        
        
        //check if login is successful and storage ok in zend_auth
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
        $this->assertEquals($this->member->id,Zend_Auth::getInstance()->getIdentity()->id);
        $this->assertEquals($this->member->userName,Zend_Auth::getInstance()->getIdentity()->userName);
        $this->assertEquals($this->member->role,Zend_Auth::getInstance()->getIdentity()->role);
        
        $this->assertEquals('access/login.phtml', $this->vr->getViewScript());
        $this->assertRedirect();
        $this->assertRedirectTo($this->url(
            array(),
            'home'
        ));
        
    }
    
    
    public function testUserLoginNotRememberMe()
    {
        
        // Prepare data for login
        
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'userName'      => $this->member->userName,
            'userPassword'  => $this->memberPass,
            'submit_login'  => $this->vr->view->translate('action_login')
        ));

        // execute login
        $this->dispatch($this->url(array(
            'action' => 'login'
        ),'access'));
        
        
        //check if login is successful and storage ok in zend_auth
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
        $this->assertEquals($this->member->id,Zend_Auth::getInstance()->getIdentity()->id);
        $this->assertEquals($this->member->userName,Zend_Auth::getInstance()->getIdentity()->userName);
        $this->assertEquals($this->member->role,Zend_Auth::getInstance()->getIdentity()->role);
         $this->assertRedirectTo($this->url(
            array(),
            'home'
        ));
    }
    
    
    
    public function testUserLoginFail()
    {
        // Prepare data for login
        $this->request->setMethod('POST');
        $this->request->setPost(array(
            'userName'      => $this->member->userName,
            'userPassword'  => 'falsepass',
            'submit_login'  => $this->vr->view->translate('action_login')
        ));
        
        // execute login
        $this->dispatch($this->url(array(
            'action' => 'login'
        ),'access'));
        
        $this->assertNotRedirect();
        
        $this->assertInstanceOf('Zend_Form',$this->vr->view->formLogin);
        $this->assertEquals(
            array('msg_wrong_password_or_username'),
            $this->vr->view->formLogin->getElement('userPassword')->getErrorMessages()
        );
    }
    
    public function testDoctrineMemberAuthenticate()
    {
        $adapter = new Pepit_Auth_Adapter_Doctrine2(
            $this->em,
            'ZC\Entity\Member',
            'userName',
            'userPassword'
        );
        
        $auth = Zend_Auth::getInstance();
        
        $adapter->setIdentity($this->member->userName);
        $adapter->setCredential($this->member->userPassword);
        
        $result = $auth->authenticate($adapter);

        //check if login is ok
        $this->assertTrue($result->isValid());
    }
    
    
    
    public function testLogout()
    {
        $this->loginUser($this->member->userName,$this->memberPass);

        // go to url
        $this->dispatch($this->url(array(
            'action' => 'logout'
        ),'access'));
        
        // check identity clearance
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
        
        // check redirection
        $this->assertRedirectTo($this->url(array(
            'action' => 'index'
        ),'access'));
    }
    
    
}
