<?php

namespace ZC\Entity;

class FormFilter extends EntityAbstract
{
    
    /** 
     * @var string $name
     */
    protected $name;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('form_filters');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('name', 'string')
                ->length(50)
                ->nullable(false)
                ->build();
        
      
    }
}