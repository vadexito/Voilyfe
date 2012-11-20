<?php

/**
 *
 * Pepit_AssociationType
 * 
 * @package Mylife
 * @author DM 
 */
abstract class Pepit_AssociationType
{
    const MAP_FIELD = '0';
    const ASSOC_ONE_TO_ONE = Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE;
    const ASSOC_MANY_TO_ONE  = Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE;
    const ASSOC_ONE_TO_MANY  = Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY;
    const ASSOC_MANY_TO_MANY  = Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY;
    
    protected $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
}

