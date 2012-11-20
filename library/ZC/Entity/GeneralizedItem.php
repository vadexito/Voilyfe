<?php

namespace ZC\Entity;

class GeneralizedItem extends EntityAbstract
{
    
    const GENERALIZED_ITEM_SINGLE_ITEM = 1;
    const GENERALIZED_ITEM_ITEM_GROUP = 2;
    
    /**
     * @Var integer $itemType
     */
    protected $itemType = 1;

    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('generalized_items');
        
        $metadata->setInheritanceType(
            \Doctrine\ORM\Mapping\ClassMetadataInfo::INHERITANCE_TYPE_JOINED
        );
        $metadata->setDiscriminatorColumn(array(
            'name' => 'disc_single_or_item_group',
            'type' => 'string',
            'length' => 100
        ));
        $metadata->setDiscriminatorMap(array(
            "generalized_items" => "ZC\Entity\GeneralizedItem",
            "single_item" => "ZC\Entity\Item",
            "itemgroup" => "ZC\Entity\ItemGroup",
        ));
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('itemType', 'integer')
                ->length(50)
                ->nullable(false)
                ->build();
    }
    
    public function __toString()
    {
        return 'item_'.$this->name;
    }
    
}