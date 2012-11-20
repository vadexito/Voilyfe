<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class FilmsEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $films_name = null;

    /**
     * @Var string
     */
    protected $films_opinion = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('films_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('films_name','string')->length(255)->nullable(false)->build();
        $builder->createField('films_opinion','string')->length(255)->nullable(false)->build();
    }


}

