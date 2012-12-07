<?php

trait Pepit_Locale_Trait
{
    
    public function findLocale()
    {
        $currentLocale = Zend_Registry::get('Zend_Locale')->findLocale();
        
        //correct zend bug in translation list
        switch($currentLocale)
        {
            case 'fr_FR' :
                $currentLocale === 'fr';
                break;
            case 'zh_CN' :
                $currentLocale === 'zh';
                break;
        }
        
        return $currentLocale;
    }
    
}