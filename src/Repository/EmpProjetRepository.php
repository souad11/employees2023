<?php

namespace App\Repository;

use App\Entity\EmpProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmpProjet>
 *
 * @method EmpProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmpProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmpProjet[]    findAll()
 * @method EmpProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmpProjet::class);
    }

//    /**
//     * @return EmpProjet[] Returns an array of EmpProjet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmpProjet
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    
public function findEmployeesByProject($projectId)
{
    return $this->createQueryBuilder('ep')
        ->select('e.firstName', 'e.lastName')
        ->join('ep.employee', 'e')
        ->andWhere('ep.projet = :projectId')
        ->setParameter('projectId', $projectId)
        ->getQuery()
        ->getResult();
}

}
