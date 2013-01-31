<?php

namespace ZC\Entity;

class Location extends EntityAbstract
{
    /** 
     * @var string $address
     */
    protected $address;
    
    /**
     *  @var double $latitude
     */
    protected $latitude = NULL;
    
    
    /** 
     * @var double $longitude
     */
    protected $longitude = NULL;
    
    /**
     * @Var ZC\Entity\Member
     */
    protected $member = null;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('locations');
        $metadata->setCustomRepositoryClass('ZC\Repository\ItemGroupRowRepository');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('address', 'string')
                ->length(255)
                ->nullable(true)
                ->build();
        
        $builder->createField('latitude', 'float')
                ->nullable(true)
                ->build();
        
        $builder->createManyToOne("member", "ZC\Entity\Member")                
                ->build();
        
        $builder->createField('longitude', 'float')
                ->nullable(true)
                ->build();
    }
    
    public function __toString()
    {
        return $this->address;
    }
    
    
}

