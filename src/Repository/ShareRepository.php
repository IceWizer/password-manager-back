<?php

namespace App\Repository;

use App\Entity\Share;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Share>
 *
 * @method Share|null find($id, $lockMode = null, $lockVersion = null)
 * @method Share|null findOneBy(array $criteria, array $orderBy = null)
 * @method Share[]    findAll()
 * @method Share[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Share::class);
    }

    /**
     * @return Share[] Returns an array of Share objects
     */
    public function findByTarget($target_id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.target = :val')
            ->setParameter('val', $target_id)
            ->andWhere('s.expireAt > :param')
            ->setParameter('param', new DateTime())
            ->orWhere('s.expireAt IS NULL')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Share
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
