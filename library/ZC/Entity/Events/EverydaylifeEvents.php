<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class EverydaylifeEvents extends \ZC\Entity\Event
{

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('everydaylife_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
    }


}

