<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class SportEvents extends \ZC\Entity\Event
{

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('sport_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
    }


}

