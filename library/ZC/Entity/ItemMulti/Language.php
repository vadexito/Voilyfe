<?php

namespace ZC\Entity\ItemMulti;
 
class Language extends \ZC\Entity\EntityAbstract
{
    /**
     * 
     * @var string $locale
     */
    protected $value;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('languages');
         
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('value', 'string')
                ->length(10)
                ->build();
        
      
    }
}

    

   