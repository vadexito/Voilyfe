<?php

namespace ZC\Entity;

class FormValidator extends EntityAbstract
{
    
    /** 
     * @var string $name
     */
    protected $name;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('form_validators');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->addField('name', 'string',array(
            'length'    => 50,
            'nullable'  => false
        ));
        
       
    }
}