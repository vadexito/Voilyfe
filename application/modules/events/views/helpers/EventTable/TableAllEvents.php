<?php

/**
* 
* creates a view for events
* 
* 
*/  

class Events_View_Helper_EventTable_TableAllEvents extends Pepit_View_Helper_EventTable_Abstract
{

    /**
     * returns a table with all events (only with date and categoryname)
     * @param array $events
     * @param mixed $options
     * @return string 
     */
    public function tableAllEvents($events,$options=NULL)
    {
        $this->setOptions($options);
        
        if (count($events) === 0)
        {
            return $this->getView()->translate('msg_noEventToShow');
        }
        
        $this->setTableHead($events);
        $this->setTableBody($events);
        
        return $this->getTable();
    }
    
    public function setTableHead($events)
    {
        $this->addHeadTh($this->getView()->translate('item_category'));
        $this->addHeadTh($this->getView()->translate('item_date'));        
    }
    
    public function setTableBody($events)
    {
        $filterDate = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => Zend_Date::DATE_MEDIUM
        ));
        //add event body of the table
        foreach ($events as $event)
        {
            $nameForTranslation = 'category_'.$event->category->name;
            $categoryName = $this->getView()->translate($nameForTranslation);
            $tr = '';
            $tr = $this->addTdToTr($this->getIcons($event->category->id,$event->id),$tr);
            $tr = $this->addTdToTr($categoryName,$tr);
            $tr = $this->addTdToTr($filterDate->filter($event->date),$tr);            
            $this->addBodyTr($tr);
        }
    }

    
}
