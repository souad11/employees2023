<?php

namespace App\Repository;

use App\Entity\DeptTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeptTitle>
 *
 * @method DeptTitle|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeptTitle|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeptTitle[]    findAll()
 * @method DeptTitle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeptTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeptTitle::class);
    }

//    /**
//     * @return DeptTitle[] Returns an array of DeptTitle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeptTitle
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
