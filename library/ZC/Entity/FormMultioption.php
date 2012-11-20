<?php

namespace ZC\Entity;

class FormMultioption extends EntityAbstract
{
    /** 
     * @var string $value
     */
    protected $value;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('form_multioptions');
         
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('value', 'string')
                ->length(100)
                ->nullable(false)
                ->build();
    }
    
}