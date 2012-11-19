<?php

/**
 * @group Controllers
 * @group Backend
 */

class InitControllerTest extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    
    protected $member;
    
    protected $memberPass;
    
    //override setup in order to cancel any file creation
    static public function setUpBeforeClass()
    { 
    }

    public function setUp()
    {
        parent::setUp();
        
        //create admin
        $this->memberPass = 'pass';
        $this->member = TestHelpersDoctrine::getMember(
            $this->em,
            'userName',
            $this->memberPass,true,true
        );
        $this->member->role = "admin";
        $this->loginUser($this->member->userName,$this->memberPass);
    }
    
    
    
    public function testInitDataBase()
    {
        $this->dispatch($this->url(array('controller' => 'init','action'=> 'initdatabase'),'backend'));
        
        $this->assertController('init');
        $this->assertAction('initdatabase');
        
        $this->assertRedirect($this->url(array('controller' => 'init'),'backend'));
    }
}
