<?php

namespace ZC\Entity\ItemMulti;

class Person extends \ZC\Entity\EntityAbstract
{
    
    /**
     * 
     * @var ZC\Entity\Member
     */    
    protected $member;
    
    /**
     * 
     * @var string $firstName
     */
    protected $firstName;

     /**
     * 
     * @var string $lastName
     */
    protected $lastName;

    /**
     * 
     * @var string $nickName
     */
    protected $nickName;
    
    /**
     * if the person is already a member otherwise NULL
     * @var ZC\Entity\Member
     */
    
    protected $memberPerson;
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('persons');
        
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createManyToOne('member', 'ZC\Entity\Member')
                ->inversedBy('persons')
                ->build();
        
        $builder->createField('firstName', 'string')
                ->columnName('first_name')
                ->length(50)
                ->nullable()
                ->build();
            
        $builder->createField('lastName', 'string')
                ->columnName('last_name')
                ->length(50)
                ->nullable()
                ->build();
            
        $builder->createField('nickName', 'string')
                ->columnName('nick_name')
                ->length(50)
                ->nullable(false)
                ->build();
        
     
    }
}



    

   