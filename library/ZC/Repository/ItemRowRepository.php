<?php

namespace ZC\Repository;

use Doctrine\ORM\EntityRepository;



/**
 * ItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRowRepository extends GeneralizedItemRowRepositoryAbstract
{
    public function findItemRowsByItemId($itemId)
    {
        return $this->createQueryBuilder('i')   
                    ->join('i.item','it')
                    ->where('it.id = :id')
                    ->setParameter('id',$itemId)
                    ->getQuery()
                    ->getResult();
    } 
    
    public function findAllByItemIdAndMemberId($itemId,$memberId)
    {
        return $this->createQueryBuilder('i')   
                    ->join('i.item','it')
                    ->join('i.member','m')
                    ->where('it.id = :id')
                    ->setParameter('id',$itemId)
                    ->andWhere('m.id = :membid')
                    ->setParameter('membid',$memberId)
                    ->getQuery()
                    ->getResult();
    } 
}