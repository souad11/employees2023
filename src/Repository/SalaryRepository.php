<?php

namespace App\Repository;

use App\Entity\Salary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Employee;

/**
 * @extends ServiceEntityRepository<Salary>
 *
 * @method Salary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salary[]    findAll()
 * @method Salary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salary::class);
    }

//    /**
//     * @return Salary[] Returns an array of Salary objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Salary
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findSalariesByEmployee(Employee $employee): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('s.fromDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
