<?php

/**
 *
 * ACL model
 * 
 * @package Mylife
 * @author DM 
 */
class Application_Acl_Acl extends Zend_Acl
{

    public function __construct()
    {
        $memberRole = new Application_Acl_MemberRole();
        
        $this->addRole(Application_Acl_Roles::GUEST)
            ->addRole($memberRole,Application_Acl_Roles::GUEST)
            ->addRole(Application_Acl_Roles::ADMIN,Application_Acl_Roles::MEMBER)
            ->addRole(Application_Acl_Roles::OWNER,Application_Acl_Roles::ADMIN)
        
            ->addResource(Application_Acl_Resources::PUBLICPAGE)
            ->addResource(Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource(Application_Acl_Resources::ADMIN_SECTION)
                
            ->addResource('locale',Application_Acl_Resources::PUBLICPAGE)
            ->addResource('error',Application_Acl_Resources::PUBLICPAGE)
            ->addResource('static-content',Application_Acl_Resources::PUBLICPAGE)
            
            ->addResource(new Application_Acl_UserResource())
            ->addResource(new Application_Acl_WithOwnerResource('events:event'))
            ->addResource(new Application_Acl_WithOwnerResource('events:itemgrouprow'))
            ->addResource(new Application_Acl_WithOwnerResource('events:itemrow'))
                
                
            ->addResource('index',Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource('access:access',Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource('categories:tree',Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource('members:settings',Application_Acl_Resources::ACCOUNT_FREE)
                
            ->addResource('backend',Application_Acl_Resources::ADMIN_SECTION)
                
            ->addResource('events:admin',Application_Acl_Resources::ADMIN_SECTION)
            ->addResource('events:rest',Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource('events:ajax',Application_Acl_Resources::ACCOUNT_FREE)
            ->addResource('members:admin',Application_Acl_Resources::ADMIN_SECTION)
            
        
            ->deny(Application_Acl_Roles::GUEST)
            ->deny(Application_Acl_Roles::MEMBER)
            ->allow(Application_Acl_Roles::ADMIN)
            ->allow(Application_Acl_Roles::OWNER)  
        
            ->allow(Application_Acl_Roles::GUEST,Application_Acl_Resources::PUBLICPAGE) 
            ->allow(Application_Acl_Roles::MEMBER,Application_Acl_Resources::ACCOUNT_FREE) 
                
            ->allow(Application_Acl_Roles::GUEST,'access:access',['login','index','getpassword'])    
            ->allow(Application_Acl_Roles::GUEST,'members:user',['register'])    
            ->allow(Application_Acl_Roles::MEMBER,'access:access',['logout'])   
            ->deny(Application_Acl_Roles::MEMBER,'access:access',['index','login'])   
            ->allow(Application_Acl_Roles::MEMBER,'members:user',['index','edit','delete'])   
            ->allow(Application_Acl_Roles::MEMBER,'members:settings',['index','setpreferences'])   
                
            ->allow(Application_Acl_Roles::MEMBER,['events:event','events:itemgrouprow','events:itemrow'],['index','create','delete','edit','showall','show']);
            
        
    }
    
    public function setDynamicPermissions()
    {
        
        $this->allow(Application_Acl_Roles::MEMBER,'events:event',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'events:itemgrouprow',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'events:itemrow',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'members:user',['edit','delete'],new Application_Acl_OwnerAssertion());
      
    }
    
    

}

