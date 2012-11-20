<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class RestaurantEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $restaurant_meals = null;

    /**
     * @Var float
     */
    protected $restaurant_price = null;

    /**
     * @Var ZC\Entity\ItemRow
     */
    protected $restaurant_mealtype = null;

    /**
     * @Var ZC\Entity\ItemGroupRow
     */
    protected $restaurant_restaurants = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('restaurant_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('restaurant_meals','string')->length(255)->nullable(true)->build();
        $builder->createField('restaurant_price','float')->length(255)->nullable(true)->build();
        $builder->createManyToOne('restaurant_mealtype','ZC\Entity\ItemRow')->build();
        $builder->createManyToOne('restaurant_restaurants','ZC\Entity\ItemGroupRow')->build();
    }


}

