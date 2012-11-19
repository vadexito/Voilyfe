<?php

require_once APPLICATION_PATH.'/modules/events/controllers/Abstract/Abstract.php';
require_once APPLICATION_PATH.'/modules/events/controllers/EventController.php';
require_once APPLICATION_PATH.'/modules/events/controllers/helpers/LastVisited.php';

/**
 * @group Controllers
 * @group Events
 * 
 */
class EventControllerPredispatchLastVisitedTest
    extends Pepit_Test_ControllerTestCaseWithDoctrine
{
    protected $_lastVisited;
    
    
    public function setup()
    {
        parent::setUp();
        
        $_SERVER['HTTP_HOST'] = 'mylife';
        $_SERVER['HTTPS'] = 'off';
        
        //mock authorization process
        $memberMock = new stdClass();
        $memberMock->id = 1;
        $memberMock->userName = 'userName';
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write($memberMock);
        
        $this->dispatch('/');
        
        $this->_lastVisited = new Events_Controller_Action_Helper_LastVisited();
        
    }
    
    public function testRememberLastVisited()
    {
        $prevUrl = 'prevUrl';
        $currentUrl = 'curUrl';
        
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost(array('askComeBack' => 'true'));
        $this->getRequest()->setRequestUri($currentUrl);
        
        $controller = new Events_EventController(
                $this->getRequest(), 
                $this->getResponse(),
                array()
        );
        $this->_lastVisited->setActionController($controller);
        
        
        $_SERVER['HTTP_REFERER'] = $prevUrl;
        
        $controller->preDispatch();
        
        $this->assertEquals($prevUrl, $this->_lastVisited->getLastVisited());
    }
    
    public function testResetLastVisited()
    {
        $prevUrl = 'prevUrl';
        $currentUrl = 'curUrl';
        $_SERVER['HTTP_REFERER'] = $prevUrl;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost(array('askComeBack' => 'false'));
        $this->getRequest()->setRequestUri($currentUrl);
        $controller = new Events_EventController(
                $this->getRequest(), 
                $this->getResponse(),
                array()
        );
        $this->_lastVisited->setActionController($controller);
        
        $controller->preDispatch();
        $this->assertEquals('', $this->_lastVisited->getLastVisited());
    }
    
    /**
     *
     * @dataProvider askComeBackOption 
     */
    public function testComingBackFromLastVisited($option)
    {
        $prevUrl = 'http://mylife/previousUrl';
        $currentUrl = '/currentUrl';
        
        $this->getRequest()->setMethod('POST');
        if ($option)
        {
            $this->getRequest()->setPost(array('askComeBack' => 'false'));
        }
        $this->getRequest()->setRequestUri($currentUrl);
        
        $controller = new Events_EventController(
                $this->getRequest(), 
                $this->getResponse(),
                array()
        );
        
        $this->_lastVisited->setActionController($controller);
        $this->_lastVisited->addLastVisited($prevUrl);
        $this->_lastVisited->addLastVisited('http://mylife'.$currentUrl);
       
         
        $_SERVER['HTTP_REFERER'] = $prevUrl;
        
        $controller->preDispatch();
        
        $this->assertEquals($prevUrl, $this->_lastVisited->getLastVisited());
    }
    
    public function askComeBackOption()
    {
       return array(
           array(true),
           array(false)
       ); 
    }
    
    
    /**
     *
     * @dataProvider askComeBackOption 
     */
    public function testNotChangingLastVisited($option)
    {
        $prevUrlFull = 'http://mylife/prevUrl';
        $currentUrl = '/currentUrl';
        $currentUrlFull = 'http://mylife/currentUrl';
        
        
        $this->getRequest()->setMethod('POST');
        if ($option)
        {
            $this->getRequest()->setPost(array('askComeBack' => 'true'));
            $_SERVER['HTTP_REFERER'] = $prevUrlFull;
        }
        else
        {
            $_SERVER['HTTP_REFERER'] = $currentUrlFull;
        }
        $this->getRequest()->setRequestUri($currentUrl);
        
        $controller = new Events_EventController(
                $this->getRequest(), 
                $this->getResponse(),
                array()
        );
        $this->_lastVisited->setActionController($controller);
        $this->_lastVisited->addLastVisited($prevUrlFull);
        
        $controller->preDispatch();
        
        //check the url has not been inserted a second time
        $this->assertEquals($prevUrlFull, $this->_lastVisited->getLastVisited());
        $this->_lastVisited->resetLastVisited();
        $this->assertEquals('', $this->_lastVisited->getLastVisited());
    }
    
}
