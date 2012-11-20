<?php

class Events_View_Helper_GetJsonDates extends Zend_View_Helper_Abstract
{
    
    
    
    
    
    public function getJsonDates($eventsPerDay)
    {
        $dates = array();
        foreach ($eventsPerDay as $event)
        {
            $dates[] = $event['date']['year']
                    .'-'.$this->_prependZero($event['date']['month'])
                    .'-'.$this->_prependZero($event['date']['day']);
        }
        
        return Zend_Json::encode($dates,Zend_Json::TYPE_ARRAY);
    }
    
    protected function _prependZero($int)
    {
        return (($int < 10) ? '0' : '').$int;
    }
}
