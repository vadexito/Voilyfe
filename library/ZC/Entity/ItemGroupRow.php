<?php

namespace ZC\Entity;

abstract class ItemGroupRow extends EntityAbstract
{

     /**
     * @Var ZC\Entity\ItemGroup
     */
    protected $itemGroup = null;

    
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

    
    public function setMember(Member $member)
    {
        $this->member = $member;
        $member->addItemGroupRow($this);
    }
    
    
    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('item_group_rows');
        $metadata->setCustomRepositoryClass('ZC\Repository\ItemGroupRowRepository');
        
        $metadata->setInheritanceType(
            \Doctrine\ORM\Mapping\ClassMetadataInfo::INHERITANCE_TYPE_JOINED
        );
        $metadata->setDiscriminatorColumn(array(
            'name' => 'disc_item_group',
            'type' => 'string',
            'length' => 100
        ));
        
        $metadata->setDiscriminatorMap(self::createDiscriminator());
        
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        
        $builder->createManyToOne("itemGroup", "ZC\Entity\ItemGroup")
                ->build();
        
        $builder->createManyToOne("member", "ZC\Entity\Member")
                ->inversedBy("itemGroupRows")
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
        require_once APPLICATION_PATH.'/modules/backend/models/Interface/Container.php';
        require_once APPLICATION_PATH.'/modules/backend/models/Abstract/Container.php';
        require_once APPLICATION_PATH.'/modules/backend/models/ItemGroups.php';
        
        //define tables in the discriminator map
        $discriminatorMap = array("itemGroupRows" => "ZC\Entity\ItemGroupRow");
        
        $discriminatorMap = self::_addDirToDiscriminator(
            \Backend_Model_ItemGroups::getContainerForRowsPath(),
            $discriminatorMap        
        );
        return $discriminatorMap;
    }
    
    static private function _addDirToDiscriminator($path,$discriminatorMap)
    {
        $suffix = \Backend_Model_ItemGroups::CONTAINER_ENTITY_SUFFIX ;
        
        $pattern = '#^(.*)'.$suffix.'.php$#';
        $dir = opendir($path);
        while($file = readdir($dir))
        {
            if (preg_match($pattern,$file))
            {
                $categoryName = preg_replace($pattern,'$1',$file);
                if ($categoryName != '')
                {
                    $discriminatorMap [$categoryName] =
                    \Backend_Model_ItemGroups::getRowContainerEntityName(
                            $categoryName
                    );
                }
            }
        }
        closedir($dir);
        return $discriminatorMap;
    }
    
    public function __toString()
    {
        $itemName = $this->itemGroup->identifierItem->name;
        $propItemGroupRow = \Events_Model_ItemGroupRows::getPropertyName(
                $this->itemGroup->name,
                $itemName
        );
        
        return (string)$this->$propItemGroupRow;
    }
}

