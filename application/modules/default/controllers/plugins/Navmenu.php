<?php

class Application_Controller_Plugin_Navmenu extends Zend_Controller_Plugin_Abstract
{
    protected $_nav;
    
    protected $_auth;
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        //get $nav
        $this->_nav = Zend_Controller_Front::getInstance()->getParam('bootstrap')
                            ->getResource('view')
                            ->navigation();
        
        //sets class for ul
        $this->_nav->menu()->setUlClass('nav pull-right');
        
        $this->_auth = Zend_Auth::getInstance();
        
        if ($this->_auth->hasIdentity())
        {
            $this->_createUserMenu();
            
            if ( $request->getModuleName() === 'events')
            {
                $this->_createCategoryMenu(); 
            }            
        }
    }
    
    protected function _createCategoryMenu()
    {
        $model = new Events_Model_Events();
        
        if ($this->_nav->findById('event') && 
                $this->_nav->findById('metaCategory'))
        {
            // get list of events and plikets
            $categories = Zend_Registry::get('entitymanager')
                            ->getRepository('ZC\Entity\Category')
                            ->findAll();

            //update event menu                
            $pageEvent = $this->_nav->findById('event');
            $pageEventAdd = $this->_nav->findById('eventAdd');
            $pageMetaCategory = $this->_nav->findById('metaCategory');
            foreach($categories as $category)
            {
                $events = $model->findEventsByCategoryByMemberIdOrderByDateDesc(
                                            $this->_auth->getIdentity()->id,
                                            $category);
                $newPage = Zend_Navigation_Page::factory(array( 
                    'label'         => 'category_'.$category->name,
                    'params'        => array(
                        'action' => 'index',
                        'containerId' => $category->id),                
                    'route'         => 'event',
                    'order'         => 1,
                    'events'        => $events,
                    'categoryId'    => $category->id
                ));
                
                //single category (not meta)
                if ($category->categories->count() === 0)
                {
                    $newPage->set('isMeta',false);
                    $pageEvent->addPage($newPage);
                    $pageMetaCategory->addPage(clone $newPage); 
                    
                    $params = array(
                        'action' => 'create',
                        'containerId' => $category->id
                    );
                    $newPage->setParams($params);
                    $pageEventAdd->addPage($newPage);
                }
                else
                //meta category
                {
                    $newPage->set('isMeta',true);
                    $pageMetaCategory->addPage($newPage); 
                }
            }
        }
    }
    
    protected function _createUserMenu()
    {
        //add user edit function
        if ($this->_nav->findById('user'))
        {
            $member = $this->_auth->getIdentity();
            $userMenu = $this->_nav->findById('user');
            $userMenu->setLabel(ucfirst($member->userName));
            
            $userPage = Zend_Navigation_Page::factory(array( 
                'params'         => array(
                                        'action' => 'edit',
                                        'memberId' => $member->id
                                    ),                
                'route'          => 'member',
                'order'          => -100,
            ));
            $userPage->setLabel('menu_user_profile');
            $userMenu->addPage($userPage);
        }
    }        
}
