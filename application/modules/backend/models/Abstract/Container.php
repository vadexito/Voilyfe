<?php

/**
 *
 * interface for containers
 * 
 * @package Mylife
 * @author DM 
 */
abstract class Backend_Model_Abstract_Container extends Pepit_Model_Doctrine2 implements Backend_Model_Interface_Container
{

    const CONTAINER_ENTITY_SUFFIX = '';
    
   
    /**
     * creation a category (either for event or item group)
     * container entity using item data
     * association are unidirectional
     * therefore WARNING = for the One_To_Many,
     * the metadata is Many_To_Many plus join table
     * @param \ZC\Entity\Category $container
     * @return string $path the path of new created file
     */
    public function createContainerEntity($container)
    {
        $name = $container->name;
        // define catainer for new class to generate
        $newClass = new Zend_CodeGenerator_Php_Class();
        $suffix = static::CONTAINER_ENTITY_SUFFIX;
        $parentclassName = $this->getParentClassContainer(false);
        $modelClass = $this->getContainerRowModel();
        $containerRowModel = new $modelClass();
        
        
        // define class
        $nameClass = ucfirst($name). $suffix;
        $newClass->setName($nameClass.' extends '.$parentclassName);
        
        $tableName = Doctrine\Common\Util\Inflector::tableize(
            $name.$suffix
        );
        
        //define general docblock
        $docBlock = new Zend_CodeGenerator_Php_Docblock(array(
            'tags'             => array(
                array(
                    'name'                 => 'author',
                    'description'          => 'DM',
                ),
            ),
        ));
        
                
        $bodyConstructMethod = '';
        $loadMetadataItems = '';
        //add items
        foreach($container->items as $item)
        {
            if ($item->itemType === 
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_SINGLE_ITEM)
            {
                $containerRowClass = 'ZC\Entity\ItemRow';
            }
            elseif ($item->itemType === 
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP)
            {
                $containerRowClass = 'ZC\Entity\ItemGroupRow';
            }
                
            $itemName = $containerRowModel->getPropertyName(
                    $container->name,
                    $item->name
            );
            $itemNameSg = Pepit_Inflector::singularize($itemName);
            
            switch ($item->associationType)
            {
                case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE:
                    $var = $containerRowClass;
                    $loadMetadataItems = $loadMetadataItems
                                ."\$builder->createOneToOne('$itemName','$containerRowClass')"
                                ."->build();"."\n";                    
                    break; 
                case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY:
                    $var = '\\Doctrine\\Common\\Collections\\ArrayCollection';
                    $loadMetadataItems = $loadMetadataItems
                                ."\$builder->createManyToMany('$itemName','$containerRowClass')"."\n"
                                .'->setJoinTable(\''.$name."_$itemName')"."\n"
                                .'->addInverseJoinColumn(\''.$name."_id','id')"."\n"
                                .'->addJoinColumn(\''.$itemName."_id', 'id')"."\n"
                                ."->build();"."\n";
                    break;
                case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY:
                    $var = '\\Doctrine\\Common\\Collections\\ArrayCollection';
                    $loadMetadataItems = $loadMetadataItems
                                ."\$builder->createManyToMany('$itemName','$containerRowClass')"
                                ."->build();"."\n";
                    break;
                case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE:
                    $var = $containerRowClass;
                    $loadMetadataItems = $loadMetadataItems
                                ."\$builder->createManyToOne('$itemName','$containerRowClass')"
                                ."->build();"."\n";
                    break;

                default:
                    $var = $item->typeSQL;
                    $nullableSQL = ($item->nullableSQL)? 'true':'false';
                    $loadMetadataItems = $loadMetadataItems
                                ."\$builder->createField('$itemName','$var')"
                                ."->length($item->sizeSQL)"
                                ."->nullable($nullableSQL)"
                                ."->build();"."\n";
            
                    break;
            }
            
            //define the property of the item
            $newClass->setProperty(array(
                'name'         => $itemName,
                'visibility'   => 'protected',
                'docBlock'     => new Zend_CodeGenerator_Php_Docblock(array(
                                    'tags'  => array(
                                        array(
                                            'name'        => 'Var',
                                            'description' => $var,
                                        ),
                                    ),
                ))
            ));
            
            //define the add function for multiple elements
            if ($item->associationType === 
                        \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY ||
                $item->associationType === 
                        \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY)
            {
                $bodyConstructMethod.=
                    '$this->'
                    .$itemName
                    .' = new \\Doctrine\\Common\\Collections\\ArrayCollection();'. "\n" ;
                $newClass->setMethod(array(
                    'name'          => 'add'.ucfirst($itemNameSg),
                    'parameter'     => array('name' => $itemNameSg),
                    'body'          => '$this->'.$itemName.'[] = $'.$itemNameSg.';'
                ));
            }
        }
        if ($bodyConstructMethod != '')
        {
            $newClass->setMethod(new Zend_CodeGenerator_Php_Method(array(
                'name' => '__construct',
                'body' => $bodyConstructMethod
            )));
        }
        
        $newClass->setMethod(new Zend_CodeGenerator_Php_Method(array(
            'name'          => 'loadMetadata',
            'visibility'    => 'static',
            'parameter'     => array(
                'name' => 'metadata',
                'type'=>'\Doctrine\ORM\Mapping\ClassMetadata'
            ),
            'body'          =>  '$metadata->setTableName(\''.$tableName
                                .'\');'. "\n"
                                .'$builder = new \\Doctrine\\ORM\\Mapping\\Builder\\ClassMetadataBuilder($metadata);'."\n"
                                .$loadMetadataItems
        )));
        
        // prepare file for class
        $file = new Zend_CodeGenerator_Php_File(array(
            'classes'            => array($newClass),
            'docBlock'           => $docBlock
        ));
        
        // generate code
        $code = $file->generate();
        
        //add namespace
        $namespace = static::getNameSpaceForContainer();
        $code = preg_replace('#<\?php#',wordwrap("<?php\n\nnamespace $namespace;\n",80,"\n"),$code);
        
        // add code to file
        $path = $this->getContainerForRowsPath($container->name);
        file_put_contents($path,$code);
        
        return $path;
    }

    static public function getContainerRowModel()
    {
        return 'Events_Model_'.static::CONTAINER_ENTITY_SUFFIX;
    }
    
}

