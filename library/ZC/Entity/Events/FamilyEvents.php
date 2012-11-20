<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class FamilyEvents extends \ZC\Entity\Event
{

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('family_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
    }


}

