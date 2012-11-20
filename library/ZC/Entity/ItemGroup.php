<?php

namespace ZC\Entity;

class ItemGroup extends GeneralizedItem
{
    /** 
     * @var string $name
     */
    protected $name;
    
    /**
     * @var \ZC\Entity\FormElementClass
     * 
     */
    protected $formElementClass;
    
    /**
     * 
     * @var boolean $formRequired
     */
    protected $formRequired;
    
    /**
     * @var integer $associationType
     * 
     */
    protected $associationType;
    
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
    */
    protected $itemGroupRows = NULL;
    
    /** 
     * item viewed as container items contains only one item
     * @var \Doctrine\Common\Collections\collection     
     */
    protected $items = NULL;
    
    /** 
     * @var ZC\Entity\Item    
     */
    protected $identifierItem = NULL;
    
    
    public function addItem(GeneralizedItem $item)
    {
        $this->items[] = $item;
    }
    
    public function addItemGroupRow(ItemGroupRow $itemGroupRow)
    {
        $this->itemGroupRows[] = $itemGroupRow;
    }
    
    public function __construct()
    {
        $this->itemGroupRows = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('item_groups');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('name', 'string')
                ->length(100)
                ->unique()
                ->nullable(false)
                ->build();
        
        $builder->createManyToOne('formElementClass', "ZC\Entity\FormElementClass")
                ->build();
        
        $builder->createField('formRequired', 'boolean')
                ->columnName('form_required')
                ->build();
        
        $builder->createManyToOne('identifierItem',"ZC\Entity\Item")
                ->build();
        
        $builder->createManyToMany('itemGroupRows','ZC\Entity\ItemGroupRow')
                ->cascadePersist()
                ->build();
        
        $builder->createManyToMany('items', 'ZC\Entity\GeneralizedItem')
                ->setJoinTable('item_groups_generalized_items')
                ->addInverseJoinColumn('generalized_item_id', 'id')
                ->addJoinColumn('item_group_id', 'id')
                ->build();
        
        $builder->createField('associationType', 'integer')
                ->columnName('association_type')
                ->build();
        
    }
}

