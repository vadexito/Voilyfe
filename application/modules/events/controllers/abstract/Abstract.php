<?php

class Events_Controller_Abstract_Abstract extends Pepit_Controller_Abstract
{
    protected $aclResource;
    
    protected $paramRowName = 'containerRowId';
    
    protected $_userId;
    
    public function init()
    {
        $this->_userId = Zend_Auth::getInstance()->getIdentity()->id;
        
    }
            
            
    public function preDispatch()
    {
        $this->_checkForComingBackUrl();
        $this->_checkAclAuthor();
        
        parent::preDispatch();
    }
    
        
    protected function _checkForComingBackUrl()
    {
        $lastVisited = $this->getHelper('lastVisited');
        $urlPrev = preg_replace('#(.*)(/true)|(true)$#','$1',$this->getRequest()->getServer('HTTP_REFERER'));
        $urlCurrent = $this->getRequest()->getScheme().'://'
                            .$this->getRequest()->getHttpHost()
                            .$this->getRequest()->getRequestUri();
        $urlCurrent = preg_replace('#(.*)(/true)|(true)$#','$1',$urlCurrent);
        
        //if add button pushed remember url
        if ($this->getRequest()->getParam('askComeBack') === 'true')
        {
            $lastVisited->addLastVisited($urlPrev);
        }
        //if coming back from a coming back request
        else if ($lastVisited->getLastVisited() === $urlCurrent)
        {
            $lastVisited->resetLastVisited();
        }
        //otherwise except in the case where staying on the same page
        else if ($urlPrev != $urlCurrent)
        {
            $lastVisited->reset();
        }
    }
    
    protected function _checkAclAuthor()
    {
        
        $resource = new Application_Acl_WithOwnerResource($this->aclResource);
        
        if ($this->getRequest()->getParam($this->paramRowName))
        {
            $itemGroupRow =  $this->_model->getStorage()
                    ->find($this->getRequest()->getParam($this->paramRowName));
            
            if ($itemGroupRow)
            {
                $resource->ownerId = $itemGroupRow->member->id;
            }
        }
        
        $this->_helper->aclAuthor($resource);
        
    }
        
}

