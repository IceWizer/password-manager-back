<?php

namespace App\Repository;

use App\Entity\FieldType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldType>
 *
 * @method FieldType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldType[]    findAll()
 * @method FieldType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldType::class);
    }

    //    /**
    //     * @return FieldType[] Returns an array of FieldType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FieldType
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
