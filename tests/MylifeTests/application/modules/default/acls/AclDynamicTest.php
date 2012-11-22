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
        $this->assertTrue($this->_acl->has('members:image'));
        
    }
    
    public function providerResourceIds()
    {
        return [
            ['events:event','protectedPriv'=>['edit','delete'],'allowedPriv'=>['create']],
            ['events:itemgrouprow','protectedPriv'=>['edit','delete'],'allowedPriv'=>['create']],
            ['events:itemrow','protectedPriv'=>['edit','delete'],'allowedPriv'=>['create']],
            ['members:user','protectedPriv'=>['edit','delete'],'allowedPriv'=>['register']],
            ['members:image','protectedPriv'=>['show'],'allowedPriv'=>[]]
        ];
        
    }
    
    /**
     *
     * @dataProvider providerResourceIds 
     */
    public function testRulesProtectedPrivilegeForGuestAndAdmin($resourceId,$protectedPriv)
    {
        $resource = new Application_Acl_WithOwnerResource($resourceId,1);
        
        foreach ($protectedPriv as $privilege)
        {
            $this->assertFalse($this->_acl->isAllowed(Application_Acl_Roles::GUEST,$resource,$privilege));
            $this->assertTrue($this->_acl->isAllowed(Application_Acl_Roles::ADMIN,$resource,$privilege));
        }
    }
    
     /**
     *
     * @dataProvider providerResourceIds 
     */
    public function testRulesMemberNotAuthor($resourceId,$protectedPriv,$allowedPriv)
    {
        
        $resource = new Application_Acl_WithOwnerResource($resourceId,2);
        
        $memberRole = new Application_acl_MemberRole();
        $memberRole->memberId = 1;
        
        
        foreach ($protectedPriv as $privilege)
        {
            $this->assertFalse($this->_acl->isAllowed($memberRole,$resource,$privilege));
        }
        
        foreach ($allowedPriv as $privilege)
        {
            $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,$privilege));
        }
    }
    
     /**
     *
     * @dataProvider providerResourceIds 
     */
    public function testRulesMemberAuthor($resourceId,$protectedPriv,$allowedPriv)
    {
        
        $resource = new Application_Acl_WithOwnerResource($resourceId,1);
        
        $memberRole = new Application_acl_MemberRole();
        $memberRole->memberId = 1;
        
        
        foreach ($protectedPriv as $privilege)
        {
            $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,$privilege));
        }
        
        foreach ($allowedPriv as $privilege)
        {
            $this->assertTrue($this->_acl->isAllowed($memberRole,$resource,$privilege));
        }
    }
}
    