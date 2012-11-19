<?php

/**
 * @group Models
 * @group Default
 * 
 */


class AclStaticTest extends Pepit_Test_ControllerTestCase
{
    
    protected $_acl = null;
    
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_acl = Zend_Registry::get('acl');
        
        
    }
    
    public function testRoles()
    {
        $this->assertTrue($this->_acl->hasRole(Application_acl_Roles::GUEST));
        $this->assertTrue($this->_acl->hasRole(Application_acl_Roles::MEMBER));
        $this->assertTrue($this->_acl->hasRole(Application_acl_Roles::ADMIN));
        $this->assertTrue($this->_acl->hasRole(Application_acl_Roles::OWNER));
    }
    
    public function testResources()
    {
        $this->assertTrue($this->_acl->has('index'));
        $this->assertTrue($this->_acl->has('error'));
        $this->assertTrue($this->_acl->has('locale'));
        $this->assertTrue($this->_acl->has('static-content'));
        
        $this->assertTrue($this->_acl->has('access:access'));
        
        $this->assertTrue($this->_acl->has('members:user'));
        $this->assertTrue($this->_acl->has('events:event'));
        
        $this->assertTrue($this->_acl->has('members:admin'));
        $this->assertTrue($this->_acl->has('events:admin'));
        $this->assertTrue($this->_acl->has('categories:tree'));
        
        $this->assertTrue($this->_acl->has('backend'));
        
        
        
    }
    
    public function testRulesGuest()
    {
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'error'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'locale'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'static-content'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'access:access','login'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'access:access','index'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::GUEST,'members:user','register'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'members:user','edit'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'members:user','delete'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'members:user','index'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'index'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'events:event'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'events:event','index'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'members:admin'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'events:admin'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'backend'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::GUEST,'categories:tree'));
        
      
        
    }
    
    public function testRulesMember()
    {
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'index'));         
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'error'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'locale'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'static-content'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'access:access','login'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'access:access','index'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'access:access','logout'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'members:user','register'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'members:user','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'members:user','delete'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'members:user','index'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'events:event','index'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'events:event','create'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'events:event','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'events:event','delete'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'categories:tree','index'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'categories:tree'));
        
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'members:admin'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'events:admin'));
        $this->assertFalse($this->_acl->isAllowed(Application_acl_Roles::MEMBER,'backend'));
    }
    
    public function testRulesAdmin()
    {
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'index'));         
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'error'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'locale'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'static-content'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','register'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','login'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','access'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','delete'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:user','logout'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'events:event','index'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'events:event','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'events:event','delete'));        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'events:event','create'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'categories:tree'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'members:admin'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'events:admin'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'backend'));
    }
    
    public function testRulesOwner()
    {
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'index'));         
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'error'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'locale'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'static-content'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','register'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','login'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','access'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','delete'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:user','logout'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'events:event','index'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'events:event','edit'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'events:event','delete'));        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'events:event','create'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::ADMIN,'categories:tree'));
        
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'members:admin'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'events:admin'));
        $this->assertTrue($this->_acl->isAllowed(Application_acl_Roles::OWNER,'backend'));
    }
}
    