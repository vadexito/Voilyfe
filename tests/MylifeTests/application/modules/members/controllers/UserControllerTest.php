<?php

/**
 * @group Controllers
 * @group Members
 */

class UserControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    public function setUp()
    {
        parent::setUp();
        
        $userName = "userName";
        $this->memberPass = 'pass'; 
        $this->member =        
        $member = testHelpersDoctrine::getMember(
            $this->em,
            $userName,
            $this->memberPass,true,true
        );
        TestHelpersDoctrine::initUserRegisterItems($this->em);
        
    }


    public function testRegisterFormSentFullData()
    {
        
        // prepare data to be sent
        $this->request->setMethod('POST');
        
        $input = array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'password',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 2,
//            'languageId'                => 2,
            'submit_register'           => $this->vr->view->translate('action_register')
        );
        
        $this->request->setPost($input);
        
        // go the url
         $this->dispatch($this->url(array(
            'action' => 'register'
        ),'member'));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('register');
        
        // check the redirecting
        $this->assertRedirectTo($this->url(array(
            'action' => 'login'
        ),'access'));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(
            in_array($this->vr->view->translate('msg_user_registered'), $fm->getCurrentMessages())
        );
        
        
        // define target
        $target = array(
            'userName'                  => $input['userName'],
            'email'                     => $input['email'],
            'firstName'                 => $input['firstName'],
            'lastName'                  => $input['lastName'],
            'role'                      => "member"
        );
        
        //get user from database
        $user = $this->em->getRepository('ZC\Entity\Member')
                         ->find(2);
        
        foreach ($target as $key => $value)
        {
            $this->assertEquals($value,$user->$key);
        }
        $this->assertEquals($input['countryId'],$user->country->id);
//        $this->assertEquals($input['languageId'],$user->language->id);
        
        //check password
        $hash = new Pepit_Auth_Hash;
        $salt = $user->passwordSalt;
        $this->assertEquals(
            $hash->hashPassword(
                $input['userPassword'],
                $salt
            ),
            $user->userPassword
        );
        
        //test the date field
        $this->assertInstanceOf('\DateTime',$user->registeringDate);
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$user->registeringDate->getTimeStamp()),5);
    }
    
    public function testRegisterFormSentNotValidData()
    {
        $valuesMember = array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'userPassword'              => 'password',
            'confirmPassword'           => 'passwor',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1,
            'submit_register'           => $this->vr->view->translate('action_register')
        );
        
        
        // prepare data to be sent
        $this->request->setMethod('POST');
        $this->request->setPost($valuesMember);
        
        // go the url
        $this->dispatch($this->url(array(
            'action' => 'register'
        ),'member'));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('register');
        
        // check the redirecting
        $this->assertNotRedirect();
        
        //check populating
        $form = $this->vr->view->form;
        unset($valuesMember['submit_register']);
        $populated = $valuesMember;
        
        foreach ($populated as $key=>$value)
        {
            $this->assertEquals($value,$form->getElement($key)->getValue());
        }
    }
    
    
   
    public function testUpdateFormNoDataSent()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        // go the url
        $this->dispatch($this->url(
                array(
                    'action' => 'edit',
                    'memberId' => 1),
                'member'));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('edit');
        
         // check the redirecting
        $this->assertNotRedirect();
        
        //check populating
        $form = $this->vr->view->form;
        
        $populated = array(
            'userName'                  => $this->member->userName,
            'email'                     => $this->member->email,
            'firstName'                 => $this->member->firstName,
            'lastName'                  => $this->member->lastName,
            'countryId'                 => $this->member->country->id,
//            'languageId'                => $this->member->language->id,
        );
        
        $this->assertEquals($populated['userName'],$form->getElement('userName')->getValue());
        $this->assertEquals($populated['email'],$form->getElement('email')->getValue());
        $this->assertEquals($populated['firstName'],$form->getElement('firstName')->getValue());
        $this->assertEquals($populated['lastName'],$form->getElement('lastName')->getValue());
        $this->assertEquals($populated['countryId'],$form->getElement('countryId')->getValue());
//        $this->assertEquals($populated['languageId'],$form->getElement('languageId')->getValue());
    }
    
    public function testUpdateFormSentNotValidData()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $valuesMember = array(
            'userName'                  => 'luigi',
            'email'                     => 'bad_email_adress',
            'firstName'                 => 'bla',
            'lastName'                  => 'voila',
            'countryId'                 => 1,
//            'languageId'                => 1,
            'submit_update'             => $this->vr->view->translate('action_save')
        );
        
        // prepare data to be sent
        $this->request->setMethod('POST');
        $this->request->setPost($valuesMember);
        
        // go the url
        $this->dispatch($this->url(
                array(
                    'action' => 'edit',
                    'memberId' => 1),
                'member'));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('edit');
        $this->assertEquals('user/edit.phtml', $this->vr->getViewScript());
        
        // check the redirecting
        $this->assertNotRedirect();
        
        //check populating
        $form = $this->vr->view->form;
                
        unset($valuesMember['submit_update']);
        
        //@ML-TODO do so that username should not be changed
        $this->assertEquals(
            $valuesMember['userName'],
            $form->getElement('userName')->getValue()
        );
        unset($valuesMember['userName']);
        
        $populated = $valuesMember;
        foreach ($populated as $key => $value)
        {
            $this->assertEquals($value,$form->getElement($key)->getValue());
        }
        
    }
    
    
    public function testUpdateSuccessfullFormSentFullData()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        // prepare data to be sent
        $this->request->setMethod('POST');
        
        $input = array(
            'userName'                  => 'luigi',
            'email'                     => 'luigi@mario.it',
            'firstName'                 => 'newFirstName',
            'lastName'                  => 'newLastName',
            'countryId'                 => 2,
//            'languageId'                => 2,
            'submit_update'           => $this->vr->view->translate('action_save')
        );
        
        $this->request->setPost($input);
        
        // go the url
        $this->dispatch($this->url(
                array(
                    'action' => 'edit',
                    'memberId' => 1),
                'member'));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('edit');
        
        
        // check the redirecting
        $this->assertRedirectTo($this->url(
                array(
                    'action' => 'index',
                    'memberId' => 1
                ),
                'member'
        ));
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(
            in_array('msg_user_updated', $fm->getCurrentMessages())
        );
        
        // define target
        $target = array(
            'userName'                  => $this->member->userName,//cannot be changed
            'email'                     => $input['email'],
            'userPassword'              => $this->member->userPassword,
            'firstName'                 => $input['firstName'],
            'lastName'                  => $input['lastName'],
            'role'                      => $this->member->role
        );
        
        //get user from database
        $user = $this->em->getRepository('ZC\Entity\Member')
                         ->find(1);
        
        foreach ($target as $key => $value)
        {
            $this->assertEquals($value,$user->$key);
        }
        $this->assertEquals($input['countryId'],$user->country->id);
//        $this->assertEquals($input['languageId'],$user->language->id);
        
        
        
        //test the date field
        $this->assertInstanceOf('\DateTime',$user->registeringDate);
        $now = new \DateTime();
        $this->assertGreaterThan(abs($now->getTimestamp()-$user->registeringDate->getTimeStamp()),5);
    }
    
    
    public function testDeleteMemberPostSentAndValidated()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $this->request->setMethod('POST');
        $this->request->setPost( array(
            'submit_delete'  => $this->vr->view->translate('action_delete')
        ));
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action' => 'delete',
                'memberId' => 1),
            'member'
        ));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('delete');
        
        
        // check flash message
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'flashMessenger'
        );
        $this->assertTrue(in_array(
                    $this->vr->view->translate('msg_user_deleted'),
                    $fm->getCurrentMessages()
        ));
        
        // check the redirecting
        $this->assertRedirectTo($this->url(array(),'home'));
        
        
        //get user from database
        $user = $this->em->getRepository('ZC\Entity\Member')
                         ->find(1);
        $this->assertNull($user);
    }
    
    public function testDeleteMemberNotAuthorizedBecauseNotUser()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action' => 'delete',
                'memberId' => 2),
            'member'
        ));
        
        // check rerouting
        $this->assertRedirectTo($this->url(array(
            'action' => 'login'
        ),'access'));
        
    }
    
    public function testDeleteMemberNotPostSent()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action' => 'delete',
                'memberId' => 1),
            'member'
        ));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('delete');
        
        
        // check the redirecting
        $this->assertNotRedirect();
        
        //get user from database
        $user = $this->em->getRepository('ZC\Entity\Member')
                         ->find(1);
        
        $this->assertEquals($this->member->userName,$user->userName);
    }
    
    public function testDeleteMemberPostSentNotValidated()
    {
        $this->loginUser($this->member->userName,$this->memberPass);
        
        $this->request->setMethod('POST');
        $this->request->setPost( array(
            'submit_delete'  => 'falsebutton'
        ));
        
        // go the url
        $this->dispatch($this->url(
            array(
                'action' => 'delete',
                'memberId' => 1),
            'member'
        ));
        
        // check routing
        $this->assertController('user');
        $this->assertAction('delete');
        
        $this->assertEquals('user/delete.phtml', $this->vr->getViewScript());
        
        // check the redirecting
        $this->assertNotRedirect();
        
        //get user from database
        $user = $this->em->getRepository('ZC\Entity\Member')
                         ->find(1);
        
        $this->assertEquals($this->member->userName,$user->userName);
    }
        
}
