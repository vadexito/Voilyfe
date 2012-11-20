<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class TripEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $trip_opinion = null;

    /**
     * @Var string
     */
    protected $trip_location = null;

    /**
     * @Var float
     */
    protected $trip_duration = null;

    /**
     * @Var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $trip_persons = null;

    public function addTrip_person($trip_person)
    {
        $this->trip_persons[] = $trip_person;
    }

    public function __construct()
    {
        $this->trip_persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('trip_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('trip_opinion','string')->length(255)->nullable(false)->build();
        $builder->createField('trip_location','string')->length(255)->nullable(false)->build();
        $builder->createField('trip_duration','float')->length(255)->nullable(false)->build();
        $builder->createManyToMany('trip_persons','ZC\Entity\ItemRow')->build();
    }


}

