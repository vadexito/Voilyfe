<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class PresentEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $present_name = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $present_subcategory = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $present_type = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('present_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('present_name','string')->length(255)->nullable(false)->build();
        $builder->createManyToOne('present_subcategory','ZC\Entity\ItemRow')->build();
        $builder->createManyToOne('present_type','ZC\Entity\ItemRow')->build();
    }


}

