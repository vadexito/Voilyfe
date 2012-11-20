<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class BridgeEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $bridge_opinion = null;

    /**
     * @Var string
     */
    protected $bridge_location = null;

    /**
     * @Var float
     */
    protected $bridge_duration = null;

    /**
     * @Var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $bridge_persons = null;

    public function addBridge_person($bridge_person)
    {
        $this->bridge_persons[] = $bridge_person;
    }

    public function __construct()
    {
        $this->bridge_persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('bridge_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('bridge_opinion','string')->length(255)->nullable(false)->build();
        $builder->createField('bridge_location','string')->length(255)->nullable(false)->build();
        $builder->createField('bridge_duration','float')->length(255)->nullable(false)->build();
        $builder->createManyToMany('bridge_persons','ZC\Entity\ItemRow')->build();
    }


}

