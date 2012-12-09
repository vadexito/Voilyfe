<?php

/**
 * Class model for Events
 *
 * @author     DM
 */

class Events_Model_Events extends Pepit_Model_Doctrine2
{
    
    protected $_formClasses = array(
        'insert' => 'eventCreate',
        'update' => 'eventUpdate',
        'delete'  => 'eventDelete'
    );
    
     /**
     * entity name
     * @var array
     */
    protected $_storageName = 'ZC\Entity\Event';
    
    public function createEntityFromForm() 
    {
        $category = $this->getEntityManager()
                         ->getRepository('ZC\Entity\Category')
                         ->find($this->getForm()->getValue('categoryId'));
        
        //create new event
        $classEvent = 
            Backend_Model_Categories::getRowContainerEntityName(
                    $category->name
            );
        
        $event = new $classEvent;
        $event->category = $category;
        $event->creationDate = new \DateTime();
        $this->addMember($event);
        
        $this->_saveEntityFromForm($event);
        
        return $event;
    }
    
    
    protected function _saveEntityFromForm($event)
    {
        $event->modificationDate = new \DateTime();
        $this->getForm()->removeElement('categoryId');
        
        parent::_saveEntityFromForm($event);
        return $event;
    }
    
    /**
     * factory widget for a given category/meta category and member
     * 
     * @param string $categoryId
     * @param string $memberId
     * @return \Pepit_Widget_OverYear 
     */
    public function getWidget($categoryId,$memberId,$typeWidget='overYear')
    {
        $category = $this   ->getEntityManager()
                            ->getRepository('ZC\Entity\Category')
                            ->find($categoryId);
        
        $events = $this->findEventsByCategoryByMemberIdOrderByDateDesc(
            $memberId,
            $category
        );
        $totalOverYearBest = $this->findBestCategoryCount($category);
        $labelCategory = 'category_'.$category->name;
        
        $optionsWidget = array(
            'totalOverYearMember' => count($events),            
            'totalOverYearBest' => $totalOverYearBest,
            'label' => $labelCategory,
        );
        $widget = Pepit_Widget::factory('name',$typeWidget,$optionsWidget);
        
        return $widget;
    }
    
    /**
     * return all events in a given category or meta category
     * @param int $memberId
     * @param ZC\Entity\Category $category
     * @return array of events 
     */
    public function findEventsByCategoryByMemberIdOrderByDateDesc(
            $memberId,$category,$dateBegin = NULL,$dateEnd = NULL)
    {
        if (\Doctrine\Common\Util\ClassUtils::getClass($category) !==
                'ZC\Entity\Category')
        {
            throw new Pepit_Model_Exception('Invalid parameter. Must be a doctrine category entity.');
        }

        //for meta category
        if ($category->categories->count()>0)
        {
            $events = array();
            foreach($category->categories as $subCategory)
            {
                $events = array_merge(
                    $this->findEventsByCategoryByMemberIdOrderByDateDesc(
                            $memberId,
                            $subCategory,
                            $dateBegin,$dateEnd),
                    $events
                );
            }
            $events = $this->sortByDateDesc($events);
            return $events;
        }
        //for single category
        else
        {
            return $this->getStorage()
                    ->findEventsBySingleCategoryByMemberIdOrderByDateDesc(
                        $memberId,
                        $category->id,
                        $dateBegin,$dateEnd
            );
        }
    }
    
    /**
     * sort events by date
     * @param array of ZC\Entity\Event $events
     * @return array of ZC\Entity\Event 
     */
    public function sortByDateDesc($events)
    {
        usort($events,'Events_Model_Events::compareDate');
        return $events;
    }
    
    public function compareDate($event1,$event2)
    {
        $time1 = $event1->date->getTimeStamp();
        $time2 = $event2->date->getTimeStamp();
        if ($time1 == $time2)
        {
            return 0;
        }
        return ($time1 < $time2) ? 1 : -1;
    }
    
    
    /**
     * return all events in all categories
     * @param int $memberId
     * @param ZC\Entity\Category $category
     * @return array of events 
     */
    public function findEventsByMemberIdOrderByDateDesc($memberId)
    {
        return $this->getStorage()->findEventsByMemberIdOrderByDateDesc($memberId);
    }
    
    /**
     * find the best number of event for all members in a given category
     * 
     * @param ZC\Entity\Category $category
     * @return int 
     */
    protected function findBestCategoryCount($category)
    {
        //for meta category
        if ($category->categories->count()>0)
        {
            $members = $this->getEntityManager()
                            ->getRepository('ZC\Entity\Member')
                            ->findAll();
            $countMax = 0;
            foreach ($members as $member)
            {
                $count = $this->countEvents($category,$member);
                $countMax = max($countMax,$count);
            }
            return $countMax;
        }
        //for single category
        else 
        {
            return $this->getStorage()->findBestCategoryCount($category->id);
        }
    }
    
    /**
     * returns the number of events in a category or meta category (recursive)
     * for a given membere
     * 
     * @param ZC\Entity\Category $category
     * @param ZC\Entity\Member $member
     * @return int 
     */
    public function countEvents($category,$member)
    {
        if ($category->categories->count()>0)
        {
            $count = 0;
            foreach ($category->categories as $subCategory)
            {
                $count+=$this->countEvents($subCategory, $member);
            }
            return $count;
        }
        else
        {
            return count($this->getStorage()
                        ->findEventsBySingleCategoryByMemberIdOrderByDateDesc(
                                $member->id,
                                $category->id                                
            ));
        }
    }
    
    
    public function getWidgetsForBestCategories($nbMaxWidgets,$memberId)
    {
        $bestCategories = $this ->getStorage()
                                ->findOrderedCategoryCountsByMember($memberId);
        $widgets = array();
        $nbWidgets = min(array($nbMaxWidgets,count($bestCategories)));
        for($i=0;$i<$nbWidgets;$i++)
        {
            $widgets[] = $this->getWidget(
                    $bestCategories[$i]['category_id'],
                    $memberId
            );
        }
        return $widgets;
    }
    
    public function getWidgetsForBestMetaCategories($nbMaxWidgets,$memberId)
    {
        //@ML-TODO implement best meta categories
        $bestCategories = array(
            array('category_id'=>13),
            array('category_id'=>15));
        $widgets = array();
        $nbWidgets = min(array($nbMaxWidgets,count($bestCategories)));
        for($i=0;$i<$nbWidgets;$i++)
        {
            $widgets[] = $this->getWidget(
                    $bestCategories[$i]['category_id'],
                    $memberId
            );
        }
        return $widgets;
    }
    
    /**
     * return the common properties of a meta category (item names)
     * @param ZC\Entity\Category $category metacategory or single category
     * @return array of items 
     */
    public function findCommonSpecificProperties($category)
    {
        if (is_object($category->categories) && $category->categories->count()>0)
        {
            $result = NULL;
            foreach ($category->categories as $subCategory)
            {
                if ($result === NULL)
                {
                    $result = $this->findCommonSpecificProperties($subCategory);
                }
                else
                {
                    //return the intersection of both item sets (comparing
                    // their string value (=name through __toString function)
                    $result = array_intersect(
                        $result,
                        $this->findCommonSpecificProperties($subCategory)
                    );
                }
            }
            return $result;
        }
        else
        {
            return $category->items->toArray();
        }
    }
    
    
    public function getEventPerDay($events)
    {
        $eventMessages = array();
        foreach($events as $event)
        {
            //checking if the date is correct (no error)
            if ($event->date->getTimeStamp())
            {
                //if the date is already taken (several events on the same date)
                $time = $event->date->getTimeStamp();

                if (array_key_exists($time,$eventMessages))
                {
                    $eventMessages[$time] = array(
                        'total'     => $eventMessages[$time]['total'] + 1,
                        'categories'=> $eventMessages[$time]['categories']."\n    "
                                                            .$event->category->name
                    );
                }
                // first time the date appears
                else
                {
                    $eventMessages[$time] = array(
                        'total' => 1,
                        'categories' => "    ".$event->category->name
                    );
                }
            }
        }
        $eventDates = array();
        foreach ($eventMessages as $time => $message)
        {
            $date = new Zend_Date($time,zend_date::TIMESTAMP);
            $date = $date->toArray();
            $eventDates[] = array(
                'date'      => array(
                    'year'      => $date['year'],
                    'month'     => $date['month'],
                    'day'       => $date['day'],
                ),
                'nbEvents'   => $message['total'],
                'category'   => $message['categories']
            );
        }
        
        return $eventDates;
    }
}

