<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class CultureEvents extends \ZC\Entity\Event
{

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('culture_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
    }


}

