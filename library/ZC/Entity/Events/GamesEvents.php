<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class GamesEvents extends \ZC\Entity\Event
{

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('games_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
    }


}

