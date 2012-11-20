<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class SwimmingEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $swimming_location = null;

    /**
     * @Var float
     */
    protected $swimming_duration = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $swimming_typeswimming = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('swimming_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('swimming_location','string')->length(255)->nullable(false)->build();
        $builder->createField('swimming_duration','float')->length(255)->nullable(false)->build();
        $builder->createManyToOne('swimming_typeswimming','ZC\Entity\ItemRow')->build();
    }


}

