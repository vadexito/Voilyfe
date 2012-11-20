<?php

class Application_View_Helper_EntityManager extends Zend_View_Helper_Abstract
{
    public function entityManager()
    {
        return Zend_Registry::get('entitymanager');
    }
}
