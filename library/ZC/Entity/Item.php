<?php

namespace ZC\Entity;

class Item extends GeneralizedItem
{
    /**
     * 
     * @var string $name
     */
    protected $name;
    
    /**
     * 
     * @var string $typeSQL
     */
    protected $typeSQL;
    
    /**
    /**
     * 
     * @var integer $sizeSQL
     */
    protected $sizeSQL;
    
    /**
    /**
     * 
     * @var boolean $nullableSQL
     */
    protected $nullableSQL;
    
    
    /**
     * @var \ZC\Entity\FormElementClass
     * 
     */
    protected $formElementClass;
    
    /**
     * @var integer $associationType
     * 
     */
    protected $associationType;
    
    
    /**
     * 
     * @var string $formLabel
     */
    protected $formLabel;
    
    
    /**
     * 
     * @var boolean $formRequired
     */
    protected $formRequired;
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
    */
    protected $formMultioptions = NULL;
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
     *    
     */
    protected $formFilters = NULL;
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
     *   
     */
    protected $formValidators = NULL;
    
    /** 
     * @var \Doctrine\Common\Collections\collection     
     */
    protected $items = NULL;
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
    */
    protected $itemRows = NULL;
    
    
    public function __construct()
    {
        $this->formMultioptions= 
                            new \Doctrine\Common\Collections\ArrayCollection();
        $this->formFilters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->formValidators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->itemRows = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function addItem(GeneralizedItem $item)
    {
        $this->items[] = $item;
    }
    
    public function addItemRow(ItemRow $itemRow)
    {
        $this->itemRows[] = $itemRow;
    }
    
    public function addFormMultioption($multioption)
    {
        $this->formMultioptions[] = $multioption;
    }
    
    public function addFormValidator($validator)
    {
        $this->formValidators[] = $validator;
    }
    
    public function addFormFilter($filter)
    {
        $this->formFilters[] = $filter;
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('items');
        $metadata->addLifecycleCallback('removeITemRows','preRemove');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('name', 'string')
                ->length(100)
                ->unique()
                ->nullable(false)
                ->build();
        
        $builder->createField('typeSQL', 'string')
                ->columnName('type_SQL')
                ->length(30)
                ->nullable(false)
                ->build();
                
        $builder->createField('sizeSQL', 'integer')
                ->columnName('size_SQL')
                ->nullable(true)
                ->build();
                
        $builder->createField('nullableSQL', 'boolean')
                ->columnName('nullable_SQL')
                ->build();
                
        $builder->createField('associationType', 'integer')
                ->columnName('association_type')
                ->build();
        
        $builder->createField('formLabel', 'string')
                ->columnName('form_label')
                ->length(150)
                ->build();
                
        $builder->createManyToOne('formElementClass', "ZC\Entity\FormElementClass")
                ->build();
        
        $builder->createField('formRequired', 'boolean')
                ->columnName('form_required')
                ->build();
        
                
        $builder->createManyToMany('formFilters', "ZC\Entity\FormFilter")
                ->build();
        
        $builder->createManyToMany('formValidators', "ZC\Entity\FormValidator")
                ->build();
                
        $builder->createManyToMany('formMultioptions','ZC\Entity\FormMultioption')
                ->cascadePersist()
                ->build();
        
        $builder->createManyToMany('items', 'ZC\Entity\GeneralizedItem')
                ->setJoinTable('items_generalized_items')
                ->addInverseJoinColumn('generalized_item_id', 'id')
                ->addJoinColumn('item_id', 'id')
                ->build();
        
        $builder->createManyToMany('itemRows','ZC\Entity\ItemRow')
                ->cascadePersist()
                ->build();
    }
    
    public function removeItemRows()
    {
        foreach ($this->itemRows as $itemRow)
        {
            \Zend_Registry::get('entitymanager')->remove($itemRow);
        }
    }
    
}