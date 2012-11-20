<?php

/**
* 
* creates a view for events
* 
* 
*/  

class Events_View_Helper_EventTable_TableUniqueCategory extends Pepit_View_Helper_EventTable_Abstract
{
    protected $_specificProperties;
    
    protected $_firstEvent = NULL;

    /**
     * return a table with detailed parameter of all events
     * @param array $events
     * @param array $options possibility to change the table css class
     * @return string 
     */
    public function tableUniqueCategory($events,$options = NULL)
    {
        $this->setOptions($options);
        $this->setFirstEvent($events);
        
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
        $eventClass = new ReflectionClass($this->_firstEvent);
        $properties = $eventClass->getProperties();
        $categoryName = $this->_firstEvent->category->name;
        //criteria for selecting specific properties to type of category
        $pattern = '#^'.$categoryName.'_#';
        
        $specificProperties = array();
        
        //add other properties to the titles
        foreach ($properties as $property)
        {
            //selection of specific properties
            if (preg_match($pattern,$property->name))
            {
                //getting item name for translation purposes
                $itemName = preg_replace($pattern,'',$property->name);
                
                $nameTranslation = 'item_'.$itemName;
                $this->addHeadTh(
                        $this->getView()->translate($nameTranslation)
                );
                
                $specificProperties[] = $property;
            }
        }
        
        //add date of event
        $this->addHeadTh($this->getView()->translate('item_date'));
        $date = new stdClass();
        $date->name = 'date';
        $specificProperties[] = $date;
        
        $this->_specificProperties = $specificProperties;
    }
    
    public function setFirstEvent($events)
    {
        if (is_array($events))
        {
            $this->_firstEvent = $events[0];
            return $this->_firstEvent;
        }
        
        if (get_class($events) === 'Zend_Paginator')
        {
            $items = $events->getCurrentItems();
            $this->_firstEvent = $items[0];
            return $this->_firstEvent;
        }
        
    }
    public function setTableBody($events)
    {
        $filterDate = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => Zend_Date::DATE_MEDIUM
        ));
        $categoryId = $this->_firstEvent->category->id;
        
        //add event body of the table
        foreach ($events as $event)
        {
            $tr = '';
            $tr = $this->addTdToTr($this->getIcons($categoryId,$event->id),$tr);
            foreach($this->_specificProperties as $property)
            {
                $propertyName = $property->name;
                if (is_object($event->$propertyName) && 
                        get_class($event->$propertyName) === 'DateTime')
                {
                    $tr = $this->addTdToTr(
                            $filterDate->filter($event->date),
                            $tr
                    );
                }
                else
                {
                    $tr = $this->addTdToTr(
                            $this->getView()->translate($this->getView()
                                ->escape((string)$event->$propertyName)),
                            $tr
                    );
                }
            }
            $this->addBodyTr($tr);
        }
    }
}
