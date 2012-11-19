<?php

/**
 * @group Models
 * @group Default
 * 
 */


class AclDynamictest extends Pepit_Test_ControllerTestCase
{
    
    protected $_acl = null;
    
    public function setUp()
    {
        parent::setUp();
        
        //initialize acl file
        $this->_acl = Zend_Registry::get('acl');
        $this->_acl->setDynamicPermissions();
    }
    
    public function testRoles()
    {
        $this->assertTrue($this->_acl->hasRole(Application_acl_Roles::MEMBER));
        
    }
    
    public function testResources()
    {
        $this->assertTrue($this->_acl->has('events:event'));
        $this->assertTrue($this->_acl->has('events:itemgrouprow'));
        $this->assertTrue($this->_acl->has('members:user'));
        
    }
    
    public function providerResourceIds()
    {
        return array(
            array('events:event'),
            array('events:itemgrouprow'),
            array('events:itemrow'),
        );
        
    }
    
    /**
     *
     * @dataProvider providerResourceIds 
     */
    public function testRulesMemberAuthorIsAuthoritzed($resourceId)
    {
        $resource = new Application_Acl_WithOwnerResource($resourceId);
        $resource->ownerId = 1;
        
        $memberRole = new Application_Acl_MemberRole();
        $memberRole->memberId = 1;
        
        $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,'edit'));
        $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,'delete'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_Acl_Roles::GUEST,$resourceId,'edit'));
        $this->assertFalse($this->_acl->isAllowed(Application_Acl_Roles::GUEST,$resourceId,'delete'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,$resourceId,'edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,$resourceId,'delete'));
        
    }
    
    
     /**
     *
     * @dataProvider providerResourceIds 
     */
    public function testRulesMemberAuthorNotAuthoritzed($resourceId)
    {
        
        $resource = new Application_Acl_WithOwnerResource($resourceId);
        $resource->ownerId = 2;
        
        $memberRole = new Application_acl_MemberRole();
        $memberRole->memberId = 1;
        
        $this->assertFalse($this->_acl->isAllowed($memberRole,$resource,'edit'));
        $this->assertFalse($this->_acl->isAllowed($memberRole,$resource,'delete'));
        $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,'create'));
        $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,'index'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,$resourceId,'edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,$resourceId,'delete'));
    }
    
   
    public function testRulesMemberOwnerIsAuthoritzed()
    {
        
        $userResource = new Application_acl_UserResource();
        $userResource->ownerId = 1;
        
        $memberRole = new Application_acl_MemberRole();
        $memberRole->memberId = 1;
        
        $this->assertTrue($this->_acl->isAllowed($memberRole,$userResource,'edit'));
        $this->assertTrue($this->_acl->isAllowed($memberRole,$userResource,'delete'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_Acl_Roles::GUEST,'events:event','edit'));
        $this->assertFalse($this->_acl->isAllowed(Application_Acl_Roles::GUEST,'events:event','delete'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,'events:event','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,'events:event','delete'));
        
    }
    
    public function testRulesMemberOwnerNotAuthoritzed()
    {
        
        $userResource = new Application_acl_UserResource();
        $userResource->ownerId = 2;
        
        $memberRole = new Application_acl_MemberRole();
        $memberRole->memberId = 1;
        
        $this->assertFalse($this->_acl->isAllowed($memberRole,$userResource,'edit'));
        $this->assertFalse($this->_acl->isAllowed($memberRole,$userResource,'delete'));
        $this->assertTrue($this->_acl->isAllowed($memberRole,$userResource,'index'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,'members:user','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,'members:user','delete'));
        
    }
    
   
}
    