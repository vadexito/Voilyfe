<?php

namespace ZC\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Generalized ItemRow Repository
 *
 */
class GeneralizedItemRowRepositoryAbstract extends EntityRepository
{
    
    const CONTAINER_TYPE_CATEGORY = 'category';
    const CONTAINER_TYPE_ITEM_GROUP = 'itemGroup';
    const CONTAINER_TYPE_ITEM = 'item';
    
    protected $_tableCountsSimpleCategory = NULL;
    
    
    
    public function findCountsByContainerName($containerType)
    {
        $this->_initTableCountsSimpleCategory($containerType);
        if ($containerType != self::CONTAINER_TYPE_CATEGORY)
        {
            return $this->getTableCountsSimpleCategory($containerType);
        }
        else
        {
            $categories = $this ->getEntityManager()
                                ->getRepository('ZC\Entity\Category')
                                ->findAll();
            
            $result = array();
            foreach ($categories as $category)
            {
                $result[$category->name] = $this->findCountByCategory(
                        $category
                );
            }
            $result['total'] = $this->_tableCountsSimpleCategory['total'];
            return $result;
        }
    }
    
    public function findCountByCategory($category)
    {
        $counts = $this->getTableCountsSimpleCategory(
                self::CONTAINER_TYPE_CATEGORY
        );
        
        try
        {
            if (array_key_exists($category->name, $counts))
            {
                return $counts[$category->name];
            }
            else
            //it is a meta category
            {
                $sumCategories = 0;
                foreach ($category->categories as $category)
                {
                    $sumCategories+=$this->findCountByCategory($category);
                }
                return $sumCategories;
            }
        }
        catch (Exception $e)
        {
            throw new \Pepit_Model_Exception('Meta Category should have categories as objects or error because of'
                    .$e->getMessage());
        }
        
        
    }
    
    protected function _initTableCountsSimpleCategory($containerType)
    {
        if ($this->_tableCountsSimpleCategory === NULL)
        {
            $qb = $this->createQueryBuilder('ir');
            $counts = 
               $qb  ->join('ir.'.$containerType,'it')
                    ->select($qb->expr()->count('it'))
                    ->addSelect('it.name')
                    ->groupBy('it.name')
                    ->getQuery()
                    ->getResult();
            
            $result = array();
            $total = 0;
            foreach($counts as $count)
            {
                $name = $count['name'];
                $value = $count[1];
                $result[$name] = (int)$value;
                $total+=$value;
            }
            $result['total'] = $total;

            $this->_tableCountsSimpleCategory = $result;
        }
        
        
    }
    
    public function getTableCountsSimpleCategory($containerType)
    {
        if ($this->_tableCountsSimpleCategory === NULL)
        {
            $this->_initTableCountsSimpleCategory($containerType);
        }
        return $this->_tableCountsSimpleCategory;
    }

}