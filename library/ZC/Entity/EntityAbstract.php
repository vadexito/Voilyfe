<?php

namespace ZC\Entity;

abstract class EntityAbstract
{
 
    /**
     * 
     * @var integer $id
     */
    protected $id;

    public function __get($item)
    {
        $getterName = 'get'.ucfirst($item);
        if (method_exists($this,$getterName))
        {
            return $this->$getterName();
        }
        return $this->$item;
    }
    
    public function __set($item,$value)
    {
        $setterName = 'set'.ucfirst($item);
        if (method_exists($this,$setterName))
        {
            $this->$setterName($value);
        }
        else
        {
            $this->$item = $value;
        }
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->setMappedSuperClass();
        
        $builder->createField('id', 'integer')
                 ->isPrimaryKey()
                 ->generatedValue('AUTO')
                 ->build();
        
        
    }
    
}

    

   