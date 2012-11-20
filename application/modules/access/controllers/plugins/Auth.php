<?php

class Application_Controller_Access_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * the acl object
     *
     * @var zend_acl
     */
    private $_acl;

    /**
     * the page to direct to if there is a current
     * user but they do not have permission to access
     * the resource
     *
     * @var array
     */
    private $_noacl = array('module' => 'default',
                             'controller' => 'error',
                             'action' => 'noauth');

    /**
     * the page to direct to if there is not current user
     *
     * @var unknown_type
     */
    private $_noauth = array('module' => 'access',
                             'controller' => 'access',
                             'action' => 'index');
    
    public function __construct($acl)
    {
        $this->_acl = $acl;
    }
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // define role
        //if not logged define guest
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $role = Application_Acl_Roles::GUEST;
        }
        //if logged get role from zend_auth
        else
        {
            // get role and id of user
            $role = Zend_Auth::getInstance()->getIdentity()->role;
        }
        
        // give role to menu        
        Zend_Controller_Front::getInstance()
                            ->getParam('bootstrap')
                            ->getResource('view')
                            ->navigation()
                            ->setRole($role);  
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
       
        if ($this->_acl->has($module.':'.$controller))
        {
            $resource = $module.':'.$controller;
        }
        else if ($module === 'default')
        {
            $resource = $controller;
        }
        else
        {
            $resource = $module;
        }
        
        $privilege = $request->getActionName();
        
        
        // check ACL and if no ACL_rights then redirect
        if ($this->_acl->has($resource) &&
                            !$this->_acl->isAllowed($role,$resource,$privilege))
        {
            // check roles for redirection in case no authorization
            if ($role === Application_Acl_Roles::GUEST)
            {
                $request->setModuleName($this->_noauth['module']);
                $request->setControllerName($this->_noauth['controller']);
                $request->setActionName($this->_noauth['action']);
            } 
            else 
            {
                $request->setModuleName($this->_noacl['module']);
                $request->setControllerName($this->_noacl['controller']);
                $request->setActionName($this->_noacl['action']);
            }
        }
        
        
        //set permission for restricting action on event and user from author 
        //and user
        $this->_acl->setDynamicPermissions();
    }
}
