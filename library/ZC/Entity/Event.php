<?php

namespace ZC\Entity;

abstract class Event extends EntityAbstract
{

    /**
     * @Var ZC\Entity\Category
     */
    protected $category = null;

    /**
     * @Var \DateTime $date
     */
    protected $date = null;
    
    /**
     * @Var ZC\Entity\Location $location
     */
    protected $location = null;
    
    /**
     * @Var ZC\Entity\Image $image
     */
    protected $image = null;
    
    /**
     * @Var arraycollection $tags
     */
    protected $tags = null;
    
    /**
     * @Var arraycollection $tags
     */
    protected $persons = null;
    
    
    /**
     * @Var ZC\Entity\Member
     */
    protected $member = null;

    /**
     * @Var \DateTime $creationDate
     */
    protected $creationDate = null;

    /**
     * @Var \DateTime $modificationDate
     */
    protected $modificationDate = null;
    
    public function addPerson($person)
    {
        $this->persons[] = $person;
    }
    
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }
    
    
    public function __construct()
    {
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setMember(Member $member)
    {
        $this->member = $member;
        $member->addEvent($this);
    }
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('events');
        $metadata->setCustomRepositoryClass('ZC\Repository\EventRepository');
        $metadata->setInheritanceType(
            \Doctrine\ORM\Mapping\ClassMetadataInfo::INHERITANCE_TYPE_JOINED
        );
        $metadata->setDiscriminatorColumn(array(
            'name' => 'disc_category',
            'type' => 'string',
            'length' => 100
        ));
        
        $metadata->setDiscriminatorMap(self::createDiscriminator());
        
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createManyToOne("category", "ZC\Entity\Category")
                ->build();
        
        $builder->createManyToOne("member", "ZC\Entity\Member")
                ->inversedBy("events")
                ->build();
        
        
        $builder->createField('date', 'datetime')
                ->columnName('date')
                ->build();
        
        $builder->createManyToOne("location", "ZC\Entity\Location")
                ->build();
        
        $builder->createOneToOne("image", "ZC\Entity\Image")
                ->build();
        
        $builder->createManyToMany('tags','ZC\Entity\ItemRow')
                ->setJoinTable('tags_itemrow')
                ->build();
        
        $builder->createManyToMany('persons','ZC\Entity\ItemRow')
                ->setJoinTable('persons_itemrow')
                ->build();
        
        $builder->createField('creationDate', 'datetime')
                ->columnName('creation_date')
                ->build();
        
        $builder->createField('modificationDate', 'datetime')
                ->columnName('modification_date')
                ->build();
    }
    
        
    
    static public function createDiscriminator()
    {
        //define tables in the discriminator map
        $discriminatorMap = array("events" => "ZC\Entity\Event");
        
        $discriminatorMap = self::_addDirToDiscriminator(
            APPLICATION_PATH.'/../library/ZC/Entity/Events/',
            $discriminatorMap        
        );
        
        return $discriminatorMap;
    }
    
    static private function _addDirToDiscriminator($path,$discriminatorMap)
    {
        //require for the doctrine cli tool only
        require_once APPLICATION_PATH.'/modules/backend/models/Interface/Container.php';
        require_once APPLICATION_PATH.'/modules/backend/models/Abstract/Container.php';
        require_once APPLICATION_PATH.'/modules/backend/models/Categories.php';
        
        $suffix = \Backend_Model_Categories::CONTAINER_ENTITY_SUFFIX ;
        
        $dir = opendir($path);
        while($file = readdir($dir))
        {
            $pattern = '#^(.*)'.$suffix.'.php$#';
            if (preg_match($pattern,$file))
            {
                $categoryName = preg_replace($pattern,'$1',$file);
                if ($categoryName != '')
                {
                    $discriminatorMap [$categoryName] =
                            \Backend_Model_Categories::getRowContainerEntityName(
                            $categoryName
                    );
                }
            }
        }
        closedir($dir);
        return $discriminatorMap;
    }
    
    
    
}

