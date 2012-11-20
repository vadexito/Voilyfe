<?php


class Pepit_Filter_DateToDateTime implements Zend_Filter_Interface
{
    public function filter($value,$format = NULL)
    {
        if (!$format)
        {
            $date = new Zend_Date(
                $value,
                Zend_Registry::get('Zend_Locale')
            );
        }
        else
        {
            $date = new Zend_Date($value,$format);
        }
        
        
        return new \DateTime(
            $date->get(Zend_Date::YEAR).'-'.
            $date->get(Zend_Date::MONTH).'-'.
            $date->get(Zend_Date::DAY)
        );
        
    }
}
