<?php

/**
* 
* creates a view for events
* 
* 
*/  

class Events_View_Helper_EventTable_TableCategory extends Pepit_View_Helper_EventTable_Abstract
{
    protected $_commonSpecificProperties;
    
    protected $_addIcons = true;
    
    /**
     * return a table with detailed parameter of all events
     * @param array $events
     * @param ZC\Entity\Category $category meta or single category
     * @param array $options 
     *  option 'class' for a css class tp the table
     *  option 'addIcons' for adding icons to each lines, by default
     *  no for metacategory and yes for single category
     * @return string 
     */
    public function tableCategory($events,$category,$options = NULL)
    {
        $this->setOptions($options);        
        
        if (count($events) === 0)
        {
            return $this->getView()->translate('msg_noEventToShow');
        }
        
        $this->setTableHead($events,$category);
        $this->setTableBody($events,$category);
        
       return $this->getTable();
    }
    
    
    public function setAddIcons($addIcons)
    {
        $this->_addIcons = $addIcons;
        return $addIcons;
    }
    
    /**
     * set table head with common specific properties and adds date and category
     * @param array $events
     * @param ZC\Entity\Category $category 
     */
    public function setTableHead($events,$category = NULL)
    {
        $model = new Events_Model_Events;
        $this->_commonSpecificProperties = $model->findCommonSpecificProperties(
            $category
        );
        
        //add other properties to the titles
        foreach ($this->_commonSpecificProperties as $item)
        {
            $nameTranslation = 'item_'.$item->name;
            $this->addHeadTh(
                    $this->getView()->translate($nameTranslation)
            );
        }
        
        //add date of event
        $this->addHeadTh($this->getView()->translate('item_date'));
    }
    
    
    public function setTableBody($events,$category = NULL)
    {
        $filterDate = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => Zend_Date::DATE_MEDIUM
        ));
        
        //add event body of the table
        foreach ($events as $event)
        {
            $tr = '';
            if ($this->_addIcons)
            {
                $tr = $this->addTdToTr($this->getIcons($category->id,$event->id),$tr);
            }
            foreach($this->_commonSpecificProperties as $item)
            {
                $eventClass = new ReflectionClass($event);
                $properties = $eventClass->getProperties();
                
                $pattern = '#(.*)'.$item->name.'$#';
                foreach ($properties as $property)
                {
                    if (preg_match($pattern, $property->name))
                    {   
                        $propertyName = $property->name;
                        $value = $event->$propertyName;
                        switch($item->associationType)
                        {
                            case Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY :
                            case Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY :
                                $stringValue = implode('-',$value->toArray());
                                break;
                            default :
                                $stringValue = (string)$value;
                        }
                        $tr = $this->addTdToTr(
                            $this->getView()->translate($this->getView()
                                ->escape($stringValue)),
                            $tr
                        );
                    }
                }
            }
            $tr = $this->addTdToTr($filterDate->filter($event->date),$tr);
            
            $this->addBodyTr($tr);
            
        } 
    }
}
