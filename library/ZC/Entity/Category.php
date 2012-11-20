<?php

namespace ZC\Entity;

class Category extends EntityAbstract
{
    /** 
     * @var string $name
     */
    protected $name;
    
    /**
     *  for meta category
     *  @var array collection
     */
    protected $categories = NULL;
    
    
    /** 
     * @var \Doctrine\Common\Collections\collection     
     */
    protected $items = NULL;
    
    
    
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function addItem(GeneralizedItem $item)
    {
        $this->items[] = $item;
    }
    
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('categories');
        $metadata->setCustomRepositoryClass('ZC\Repository\CategoryRepository');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('name', 'string')
                ->length(100)
                ->unique()
                ->nullable(false)
                ->build();
        
        $builder->createManyToMany('categories', 'ZC\Entity\Category')
                ->setJoinTable('generalized_categories')
                ->addInverseJoinColumn('generalized_category_id', 'id')
                ->addJoinColumn('category_id', 'id')
                ->build();
        
        $builder->createManyToMany('items', 'ZC\Entity\GeneralizedItem')
                ->setJoinTable('categories_generalized_items')
                ->addInverseJoinColumn('generalized_item_id', 'id')
                ->addJoinColumn('category_id', 'id')
                ->build();
    }
    
    public function __toString()
    {
        return 'category_'.$this->name;
    }
    
    
}

