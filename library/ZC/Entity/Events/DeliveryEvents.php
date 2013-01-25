<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class DeliveryEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $delivery_meals = null;

    /**
     * @Var float
     */
    protected $delivery_price = null;

    /**
     * @Var ZC\Entity\ItemGroupRow
     */
    protected $delivery_company = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('delivery_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('delivery_meals','string')->length(255)->nullable(true)->build();
        $builder->createField('delivery_price','float')->length(255)->nullable(true)->build();
        $builder->createManyToOne('delivery_company','ZC\Entity\ItemGroupRow')->build();
    }


}

