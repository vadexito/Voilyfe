<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class RunningEvents extends \ZC\Entity\Event
{

    /**
     * @Var float
     */
    protected $running_duration = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('running_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('running_duration','float')->length(255)->nullable(false)->build();
    }


}

