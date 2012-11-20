<?php

class Events_Bootstrap extends Zend_Application_Module_Bootstrap
{
    
    public function _initRest()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, array(), array(
            'events' => array('rest'),
        ));
        $router->addRoute('rest', $restRoute);
    }
}
    
   


