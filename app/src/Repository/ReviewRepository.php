<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    private string $resultType = 'daily';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return string
     */
    public function getResultType(): string
    {
        return $this->resultType;
    }

    /**
     * @param string $resultType
     */
    public function setResultType(string $resultType)
    {
        $this->resultType = $resultType;
    }

    public function getDateGroupedStatistic($hotelId, $startDate, $endDate)
    {
        $groupByDateFormats = [
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%v',
            'monthly' => '%Y-%m',
        ];

        $query = $this->createQueryBuilder('r')
            ->select('
                DATE_FORMAT(r.created_date, \'' . $groupByDateFormats[$this->getResultType()] . '\') AS date_group, 
                COUNT(r.id) AS review_count, 
                ROUND(AVG(r.score),5) AS average_score'
            )
            ->where('r.hotel_id = :hotelId')
            ->andWhere('r.created_date BETWEEN :startDate AND :endDate')
            ->setParameter('hotelId', $hotelId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('date_group')
            ->orderBy('date_group');

        return $query->getQuery()->getResult();
    }

    public function getOverTimeAverage($hotelId, $startDate)
    {
        $query = $this->createQueryBuilder('r')
            ->select('                 
                COUNT(r.id) AS overtime_review_count, 
                ROUND(AVG(r.score),5) AS overtime_average_score'
            )
            ->where('r.hotel_id = :hotelId')
            ->andWhere('r.created_date < :startDate')
            ->setParameter('hotelId', $hotelId)
            ->setParameter('startDate', $startDate);

        return $query->getQuery()->getResult()[0];
    }
}
