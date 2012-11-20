<?php

namespace ZC\Entity;

class ItemRow extends EntityAbstract
{

    /** 
     * @var string $value
     */
    protected $value;
    
     /**
     * @Var ZC\Entity\Item
     */
    protected $item = null;

    
    /**
     * @Var ZC\Entity\Member
     */
    protected $member = null;

    /**
     * @Var \DateTime $creationDate
     */
    protected $creationDate = null;

    /**
     * @Var \DateTime $modificationDate
     */
    protected $modificationDate = null;

    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('item_rows');
        $metadata->setCustomRepositoryClass('ZC\Repository\ItemRowRepository');

        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('value', 'string')
                ->length(100)
                ->nullable(false)
                ->build();
        
        $builder->createManyToOne("item", "ZC\Entity\Item")
                ->build();
        
        $builder->createManyToOne("member", "ZC\Entity\Member")
                ->inversedBy("itemGroupRows")
                ->build();
        
        
        $builder->createField('creationDate', 'datetime')
                ->nullable('false')
                ->columnName('creation_date')
                ->build();
        
        $builder->createField('modificationDate', 'datetime')
                ->nullable('false')
                ->columnName('modification_date')
                ->build();
    }
    
    public function __toString()
    {
        return (string)$this->value;
    }
}

