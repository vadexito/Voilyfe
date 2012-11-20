<?php

namespace ZC\Entity;

class Image extends EntityAbstract
{
    /** 
     * @var string $path
     */
    protected $path;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('images');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('path', 'string')
                ->length(255)
                ->nullable(true)
                ->build();
    }
    
    public function __toString()
    {
        return (string)$this->path;
    }
    
    
}

