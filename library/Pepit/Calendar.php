<?php

/**
 * Class table for calenders in event presentation
 *
 * @author     DM
 */

include_once(APPLICATION_PATH.'/../public/calendar/tc_calendar.php');

class Pepit_Calendar extends tc_calendar
{
    public function __construct($objname, $date_picker = false, $show_input = true) 
    {
        parent::__construct($objname,$date_picker,$show_input);
        $this->setIcon('/calendar/images/iconCalendar.gif');
        $this->setDate(date('d'), date('m'), date('Y'));
        $this->setPath('/calendar/');
        $this->setYearInterval(1970,2020);
        $this->showWeeks(false);
        $this->startDate(1);
        
        // possibility to include function to be executed on click on the date
        //$this->setOnChange("test()"); 
    }
    
    
    
}

