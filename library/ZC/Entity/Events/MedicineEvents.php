<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class MedicineEvents extends \ZC\Entity\Event
{

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $medicine_medicine = null;

    /**
     * @Var string
     */
    protected $medicine_quantity = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('medicine_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createManyToOne('medicine_medicine','ZC\Entity\ItemRow')->build();
        $builder->createField('medicine_quantity','string')->length(0)->nullable(false)->build();
    }


}

