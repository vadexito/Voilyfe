<?php

namespace ZC\Entity\ItemGroupRows;

/**
 * @author DM
 */


class RestaurantsItemGroupRows extends \ZC\Entity\ItemGroupRow
{

    /**
     * @Var string
     */
    protected $restaurants_phoneNumber = null;

    /**
     * @Var string
     */
    protected $restaurants_name = null;

    /**
     * @Var string
     */
    protected $restaurants_address = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $restaurants_cuisineType = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('restaurants_item_group_rows');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('restaurants_phoneNumber','string')->length(255)->nullable(true)->build();
        $builder->createField('restaurants_name','string')->length(255)->nullable(false)->build();
        $builder->createField('restaurants_address','string')->length(255)->nullable(true)->build();
        $builder->createManyToOne('restaurants_cuisineType','ZC\Entity\ItemRow')->build();
    }


}

