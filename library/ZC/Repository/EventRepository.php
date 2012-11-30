<?php

namespace ZC\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 */
class EventRepository extends GeneralizedItemRowRepositoryAbstract
{
    /**
     * return an array with all the events and sorted by date descending (new first)
     * @param int $memberId
     * @param int $categoryId
     * @param \dateTime $dateBegin, either date of begin or, if there is no 
     * dateEnd corresponds to the date requested (one day only)
     * @param \dateTime $dateEnd
     * @return array an array with all the corresponding events 
     */
    
    
    public function findEventsBySingleCategoryByMemberIdOrderByDateDesc(
        $memberId,$categoryId,$dateBegin = NULL,$dateEnd = NULL)
    {
        $query = $this->createQueryBuilder('e')   
                    ->join('e.member','m')
                    ->join('e.category','c')
                    ->where('m.id = :idMemb')
                    ->andWhere('c.id = :idCat')
                    ->setParameter('idMemb',$memberId)
                    ->setParameter('idCat',$categoryId)
                    ->orderBy('e.date','DESC');
        
        if ($dateBegin && $dateEnd)
        {
            $query  ->andWhere('e.date > :date')
                    ->setParameter('date',$dateBegin)
                    ->andWhere('e.date < :date')
                    ->setParameter('date',$dateEnd);
        }
        else if ($dateBegin)
        {
            $query  ->andWhere('e.date = :date')
                    ->setParameter('date',$dateBegin);
        }
        
        return $query->getQuery()->getResult();
    } 
    
    /**
     * returns all events for a given member
     * @param type $memberId
     * @return array of events 
     */
    public function findEventsByMemberIdOrderByDateDesc(
            $memberId)
    {
        return $this->createQueryBuilder('e')   
                    ->join('e.member','m')
                    ->where('m.id = :idMemb')
                    ->setParameter('idMemb',$memberId)
                    ->orderBy('e.date','DESC')
                    ->getQuery()
                    ->getResult();
    } 
    
    /**
     * return an array with the categories (no meta) having the most events
     * @param int $memberId
     * @return array array of categories
     */
    public function findOrderedCategoryCountsByMember($memberId)
    {
        $qb = $this->createQueryBuilder('e');
        return  $qb ->select($qb->expr()->count('e').'as total_events')
                    ->join('e.member','m')
                    ->join('e.category','c')
                    ->where('m.id = :idMemb')
                    ->setParameter('idMemb',$memberId)
                    ->addSelect('c.id as category_id')
                    ->groupBy('category_id')
                    ->orderBy('total_events','desc') //meta category have no direct event
                    ->getQuery()
                    ->getResult();
    }
    
    public function findOrderedTagsCountsByMember($memberId)
    {
        $qb = $this->createQueryBuilder('e');
        return  $qb ->select($qb->expr()->count('e').'as total_events')
                    ->join('e.member','m')
                    ->join('e.tags','t')
                    ->where('m.id = :idMemb')
                    ->setParameter('idMemb',$memberId)
                    ->addSelect('c.id as category_id')
                    ->groupBy('category_id')
                    ->orderBy('total_events','desc') //meta category have no direct event
                    ->getQuery()
                    ->getResult();
    }
    
    public function findBestCategoryCount($categoryId)
    {
        $qb = $this->createQueryBuilder('e');
        
        $total = $qb ->select($qb->expr()->count('e').'as total_events')
                    ->join('e.category','c')
                    ->join('e.member','m')
                    ->where('c.id = :id')
                    ->setParameter('id',$categoryId)
                    ->addSelect('m.id as member_id')
                    ->groupBy('member_id')
                    ->orderBy('total_events','desc')
                    ->getQuery()
                    ->getResult();
        
        return  (isset($total[0]))? $total[0]['total_events'] : 0;
    }
}