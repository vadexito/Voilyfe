<?php

/**
 * Class model for backup
 *
 * @author     DM
 */

class Backend_Model_Backup

{
    /**
     * entitymanager from doctrine
     * @var type 
     */
    protected $_em;
    
    public function __construct($em = NULL)
    {
        if ($em === NULL)
        {
            $this->_em = Zend_Registry::get('entitymanager');
        }
        else
        {
            $this->_em = $em;
        }
    }
    
    public function getEntityManager()
    {
        return $this->_em;
    }
    
    
    public function saveDataXMLAllMembers($fileDir)
    {
        $modelMembers = new Members_Model_Users();
        $modelEvents = new Events_Model_Events();
        
        foreach ($modelMembers->fetchEntries() as $member)
        {
            $this->saveDataXMLByMember(
                $member->id,
                $modelEvents->findEventsByMemberIdOrderByDateDesc($member->id),
                $fileDir
            );
        }
    }
    
    public function saveDataXMLByMember($memberId,$events,$fileDir)
    {
        
        $config = new Zend_Config(array(), true);
        
        //add events
        $config->events = array();
        
        $eventsConfig = array();
        foreach($events as $event)
        {
            $newEvent = array();
            $reflect = Doctrine\Common\Util\ClassUtils::newReflectionObject($event);
            
            foreach ($reflect->getProperties() as $property)
            {
                $name = $property->name;
                if ($name === 'category')
                {
                    $newEvent[$name] = $event->$name->name;
                }
                else
                {
                    $newEvent[$name] = $this->propertyToString($event->$name);
                }
            }
            $eventsConfig[] = $newEvent;
        }
        $config->events = array(
            'memberId'    => $memberId,
            'event'     => $eventsConfig
        );
        
        //write file
        $writer = new Zend_Config_Writer_Xml();
        $writer->write($fileDir.'member_id'.$memberId.'.xml', $config);
        
    }
    
    protected function propertyToString($propertyValue)
    {
        //case of datetime
        if ($propertyValue instanceof DateTime)
        {
            return $propertyValue->getTimeStamp();
        }
        
        //case of arraycollection
        if ($propertyValue instanceof \Doctrine\Common\Collections\ArrayCollection ||
            $propertyValue instanceof \Doctrine\ORM\PersistentCollection)
        {
            $values = array();
            foreach ($propertyValue as $value)
            {
                $values[] = (string)$value;
            }
            return implode(',',$values);
        }
        
        //in any other cases
        return (string)$propertyValue;
    }
    
    public function initializeDataBaseFromXMLMemberBackup($filePath)
    {
        $xml = new Zend_Config_Xml($filePath,'events');
        
        if (!$xml->memberId)
        {
            throw new Pepit_Model_Exception('XML File not valid.');
        }
        
        $member = $this ->getEntityManager()
                        ->getRepository('ZC\Entity\Member')
                        ->find($xml->memberId);
        if (!$member)
        {
            throw new Pepit_Model_Exception('Member does not exist.');
        }
        
        $model = new Events_Model_Events();
        $events = $model->findEventsByMemberIdOrderByDateDesc($member->id);
        if ($events)
        {
            throw new Pepit_Model_Exception('Member has already events in the database. Recover backup not possible');
        } 
        
        foreach($xml->event as $event)
        {
            $eventClass = Backend_Model_Categories::getRowContainerEntityName(
                    $event->category
            );
            $eventEntity = new $eventClass;
            
            $eventEntity->date = new DateTime($event->date);
            $eventEntity->modificationDate = new DateTime($event->modificationDate);
            $eventEntity->creationDate = new DateTime($event->creationDate);
            $eventEntity->member = $member;
            $category = $this
                                ->getEntityManager()
                                ->getRepository('ZC\Entity\Category')
                                ->findOneByName($event->category);
            $eventEntity->category = $category;
            
            foreach ($category->items as $item)
            {
                $associationType = $item->associationType;
                
                
                
                
                
                
            }
            
            
            $this->getEntityManager()->persist($eventEntity);
        }
        $this->getEntityManager()->flush();
    }
    
    
    
    
    protected function stringToProperty($string,$propertyName)
    {
        
    }
}

