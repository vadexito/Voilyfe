<?php

namespace ZC\Entity\ItemMulti;

class Country extends \ZC\Entity\EntityAbstract
{
    
    /**
     * 
     * @var string $value
     */
    protected $value;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('countries');
         
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('value', 'string')
                ->length(50)
                ->build();
        
       
    }
}

    

   