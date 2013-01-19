<?php

namespace ZC\Entity\ItemGroupRows;

/**
 * @author DM
 */


class RestaurantItemGroupRows extends \ZC\Entity\ItemGroupRow
{

    /**
     * @Var string
     */
    protected $restaurant_phoneNumber = null;

    /**
     * @Var string
     */
    protected $restaurant_name = null;

    /**
     * @Var string
     */
    protected $restaurant_address = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $restaurant_cuisineType = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setCustomRepositoryClass('ZC\Repository\ItemGroupRowRepository');
        
        $metadata->setTableName('restaurant_item_group_rows');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('restaurant_phoneNumber','string')->length(255)->nullable(true)->build();
        $builder->createField('restaurant_name','string')->length(255)->nullable(false)->build();
        $builder->createField('restaurant_address','string')->length(255)->nullable(true)->build();
        $builder->createManyToOne('restaurant_cuisineType','ZC\Entity\ItemRow')->build();
    }


}

