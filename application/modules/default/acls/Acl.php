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
        
        $this->addRole(new Zend_Acl_Role(Application_Acl_Roles::GUEST))
            ->addRole($memberRole,Application_Acl_Roles::GUEST)
            ->addRole(new Zend_Acl_Role(Application_Acl_Roles::ADMIN),Application_Acl_Roles::MEMBER)
            ->addRole(new Zend_Acl_Role(Application_Acl_Roles::OWNER),Application_Acl_Roles::ADMIN)
        
            ->add(new Zend_Acl_Resource(Application_Acl_Resources::PUBLICPAGE))
            ->add(new Zend_Acl_Resource(Application_Acl_Resources::ACCOUNT_FREE))
            ->add(new Zend_Acl_Resource(Application_Acl_Resources::ADMIN_SECTION))
                
            ->add(new Zend_Acl_Resource('locale'),Application_Acl_Resources::PUBLICPAGE)
            ->add(new Zend_Acl_Resource('error'),Application_Acl_Resources::PUBLICPAGE)
            ->add(new Zend_Acl_Resource('static-content'),Application_Acl_Resources::PUBLICPAGE)
            
            ->add(new Application_Acl_UserResource())
            ->add(new Application_Acl_WithOwnerResource('events:event'))
            ->add(new Application_Acl_WithOwnerResource('events:itemgrouprow'))
            ->add(new Application_Acl_WithOwnerResource('events:itemrow'))
                
                
            ->add(new Zend_Acl_Resource('index'),Application_Acl_Resources::ACCOUNT_FREE)
            ->add(new Zend_Acl_Resource('access:access'),Application_Acl_Resources::ACCOUNT_FREE)
            ->add(new Zend_Acl_Resource('categories:tree'),Application_Acl_Resources::ACCOUNT_FREE)
            ->add(new Zend_Acl_Resource('members:settings'),Application_Acl_Resources::ACCOUNT_FREE)
                
            ->add(new Zend_Acl_Resource('backend'),Application_Acl_Resources::ADMIN_SECTION)
                
            ->add(new Zend_Acl_Resource('events:admin'),Application_Acl_Resources::ADMIN_SECTION)
            ->add(new Zend_Acl_Resource('events:rest'),Application_Acl_Resources::ACCOUNT_FREE)
            ->add(new Zend_Acl_Resource('events:ajax'),Application_Acl_Resources::ACCOUNT_FREE)
            ->add(new Zend_Acl_Resource('members:admin'),Application_Acl_Resources::ADMIN_SECTION)
            
        
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
                
            ->allow(Application_Acl_Roles::MEMBER,'events:event',[
                'index','create','delete','edit'
            ])
                
            ->allow(Application_Acl_Roles::MEMBER,'events:event',['edit','delete'])
            ->allow(Application_Acl_Roles::MEMBER,'events:itemgrouprow',['index','create','delete','edit','showall'])
            ->allow(Application_Acl_Roles::MEMBER,'events:itemrow',['index','create','delete','edit','showall']);
            
        
        
        
    }
    
    public function setDynamicPermissions()
    {
        
        $this->allow(Application_Acl_Roles::MEMBER,'events:event',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'events:itemgrouprow',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'events:itemrow',['edit','delete'],new Application_Acl_OwnerAssertion());
        $this->allow(Application_Acl_Roles::MEMBER,'members:user',['edit','delete'],new Application_Acl_OwnerAssertion());
      
    }
    
    

}

