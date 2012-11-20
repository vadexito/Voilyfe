<?php

namespace ZC\Entity\ItemGroupRows;

/**
 * @author DM
 */


class CompanyItemGroupRows extends \ZC\Entity\ItemGroupRow
{

    /**
     * @Var string
     */
    protected $company_phoneNumber = null;

    /**
     * @Var string
     */
    protected $company_name = null;

    /**
     * @Var string
     */
    protected $company_address = null;

    static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->setTableName('company_item_group_rows');
        $builder = new \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder($metadata);
        $builder->createField('company_phoneNumber','string')->length(255)->nullable(true)->build();
        $builder->createField('company_name','string')->length(255)->nullable(false)->build();
        $builder->createField('company_address','string')->length(255)->nullable(true)->build();
    }


}

