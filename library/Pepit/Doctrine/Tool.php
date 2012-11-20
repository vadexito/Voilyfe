<?php

class Pepit_Doctrine_Tool 
{
    
    /**
     *
     * @param Doctrine entitymanager $em entitymanager
     * @param string $entityName 
     */
    
    public static function updateDoctrineSchemaForCreatingNewContainer(
                                                                $em,$entityName)
    {
        // define tool cli for doctrine and execute create entity
        $tool = new Doctrine\ORM\Tools\SchemaTool($em);
        
       //update for itemgrouprow discriminator map
        $discriminatorMap = 
            call_user_func_array(
                array($entityName,'createDiscriminator'),
                array()
            );
        $em->getClassMetadata($entityName)
                ->subClasses = array_values($discriminatorMap);
        $em->getClassMetadata($entityName)
                ->discriminatorMap= $discriminatorMap;
        
        $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
    }
    
    public static function updateDoctrineSchema($em)
    {
        // define tool cli for doctrine and execute update
        $tool = new Doctrine\ORM\Tools\SchemaTool($em);
        $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
    }
    
    public static function toString($event,$property)
    {
        if (!property_exists($event,$property))
        {
            throw new Pepit_Model_Exception("Property doesn't exist");
        }
        $element = $event->$property;
        if ($element instanceof \Doctrine\ORM\PersistentCollection ||
            $element instanceof Doctrine\Common\Collections\ArrayCollection)
        {
            return implode(', ',$element->toArray());
        }
        else if (is_object($element))
        {
            return $element->__toString();
        }
        else
        {
            return $element;
        }
        
    }
    
}
