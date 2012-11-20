<?php

namespace ZC\Entity;

use Doctrine\Common\Collections\ArrayCollection;
 
class Member extends EntityAbstract
{
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
     * @var string $userName
     */
    protected $userName;
    
    /**
     * 
     * @var string $userPassword
     */
    protected $userPassword;
    
    /**
     * 
     * @var string $passwordSalt
     */
    protected $passwordSalt;
    
   
    
    /**
     * 
     * @var string $email
     */
    protected $email;
    
    /**
     * 
     * @var \DateTime $registeringDate
     */
    protected $registeringDate;
    
    /**
     * 
     * @var string $role
     */
    protected $role;
    
    /**
     * 
     * @var ZC\Entity\ItemMulti\Country $country
     */
    protected $country;
    
     /**
     * 
     * @var ZC\Entity\ItemMulti\Language $language
     */
    protected $language;
    
    /**
     * 
     * @var Doctrine\Common\Collection\ArrayCollection $persons
     */    
    protected $persons = NULL;
    
    /**
     * 
     * @var Doctrine\Common\Collection\ArrayCollection $events
     */
    protected $events;
    
    /**
     * 
     * @var Doctrine\Common\Collection\ArrayCollection $events
     */
    protected $itemGroupRows;
    
    public function __construct()
    {
        $this->persons = new ArrayCollection();
        $this->events = new ArrayCollection();
    }
    
    public function addPerson($person)
    {
        $this->persons[] = $person;
        $person->member = $this;
    }
    
    public function addEvent($event)
    {
        $this->events[] = $event;
    }
    
    public function addItemGroupRow($itemGroupRow)
    {
        $this->itemGroupRows[] = $itemGroupRow;
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('members');
        $metadata->setCustomRepositoryClass('ZC\Repository\MemberRepository');
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createField('firstName', 'string')
                ->columnName('first_name')
                ->length(30)
                ->nullable()
                ->build();
            
        $builder->createField('lastName', 'string')
                ->columnName('last_name')
                ->length(30)
                ->nullable()
                ->build();
            
        $builder->createField('userName', 'string')
                ->columnName('user_name')
                ->length(30)
                ->unique()
                ->nullable(false)
                ->build();
            
        $builder->createField('userPassword', 'string')
                ->columnName('user_password')
                ->length(255)
                ->nullable(false)
                ->build();
            
        $builder->createField('passwordSalt', 'string')
                ->columnName('password_salt')
                ->length(255)
                ->nullable(false)
                ->build();
            
        $builder->createField('email', 'string')
                ->columnName('email')
                ->length(150)
                ->nullable(false)
                ->build();
            
        
        $builder->createOneToMany('persons','ZC\Entity\ItemMulti\Person')
                ->mappedBy('member')
                ->cascadeRemove()
                ->build();
        
        
        $builder->createOneToMany('events','ZC\Entity\Event')
                ->mappedBy('member')
                ->cascadeRemove()
                ->build();
        
        $builder->createOneToMany('itemGroupRows','ZC\Entity\ItemGroupRow')
                ->mappedBy('member')
                ->cascadeRemove()
                ->build();
        
        $builder->createField('registeringDate', 'datetime')
                ->columnName('registering_date')
                ->build();
        
        $builder->createField('role', 'string')
                ->length(20)
                ->nullable(false)
                ->build();
        
        $builder->addManyToOne('country', 'ZC\Entity\ItemMulti\Country');
        $builder->addManyToOne('language', 'ZC\Entity\ItemMulti\Language');
        
    
        
    }
    
    public function __toString()
    {
        return $this->userName;
    }
}
