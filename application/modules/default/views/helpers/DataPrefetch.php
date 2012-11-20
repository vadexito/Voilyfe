<?php

class Application_View_Helper_DataPrefetch extends Zend_View_Helper_Abstract
{
    public function dataPrefetch()
    {
                
        if (Zend_Registry::get('config')->get('html')
            && Zend_Registry::get('config')->html->link->data->prefetch === "false")
        {
            return '';
        }
        return "data-prefetch";
    }
}
