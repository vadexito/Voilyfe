<?php

/**
 *
 * localeController 
 * @package Mylife
 * @author DM 
 */

class LocaleController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $session = new Zend_Session_Namespace('Mylife_locale');
        $session->locale = $this->getRequest()->getParam('localeName');

        $url = $this->getRequest()->getServer('HTTP_REFERER');
        $this->_redirect($url);
    }
            

}

