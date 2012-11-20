<?php

abstract class Pepit_Controller_Abstract_Abstract extends Zend_Controller_Action
{ 
    public function preDispatch()
    {
        $this->_helper->dispatchToMobileFile();
    }
}

