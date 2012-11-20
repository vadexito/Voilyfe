<?php

namespace ZC\Entity;

class FormElementClass extends EntityAbstract
{
    
    /** 
     * @var string $name
     */
    protected $name;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('form_element_classes');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('name', 'string')
                ->length(100)
                ->nullable(false)
                ->build();
        
        
    }
}