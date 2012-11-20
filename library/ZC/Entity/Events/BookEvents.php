<?php

namespace ZC\Entity\Events;

/**
 * @author DM
 */


class BookEvents extends \ZC\Entity\Event
{

    /**
     * @Var string
     */
    protected $book_name = null;

    /**
     * @Var string
     */
    protected $book_opinion = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('book_events');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('book_name','string')->length(255)->nullable(false)->build();
        $builder->createField('book_opinion','string')->length(255)->nullable(false)->build();
    }


}

