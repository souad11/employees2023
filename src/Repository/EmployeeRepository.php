<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

//    /**
//     * @return Employee[] Returns an array of Employee objects
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

//    public function findOneBySomeField($value): ?Employee
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findBySearchTerm($searchTerm, $sortField1, $sortOrder1, $sortField2, $sortOrder2)
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.firstName LIKE :searchTerm OR e.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');

        $allowedFields = ['id', 'birthDate', 'firstName', 'lastName', 'gender', 'hireDate']; // Add other fields as needed

        if (in_array($sortField1, $allowedFields)) {
            $queryBuilder->addOrderBy("e.$sortField1", $sortOrder1);
        }

        if (in_array($sortField2, $allowedFields) && $sortField2 !== $sortField1) {
            $queryBuilder->addOrderBy("e.$sortField2", $sortOrder2);
        }

        return $queryBuilder

            ->getQuery()
            ->getResult();
    }

    public function findAllEmployeesWithSalaries(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.salaries', 's')
            ->addSelect('s')
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //obtenir les salaires d'un employé
    public function findEmployeeWithSalaries($id): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.salaries', 's')
            ->addSelect('s')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.fromDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //obtenir le dernier salaire, c'est celui avec  la de fin de contrat à 9999-01-01
    public function findLastSalary(int $employeeId)
    {
        return $this->createQueryBuilder('e')
            ->select('s')
            ->from('App\Entity\Salary', 's')
            ->andWhere('s.employee = :employeeId')
            ->andWhere('s.toDate = :toDate')
            ->setParameter('employeeId', $employeeId)
            ->setParameter('toDate', new \DateTime('9999-01-01'))
            ->orderBy('s.fromDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }   
    /**
     * @param int $employeeId
     * @return mixed
     */

    //obtenir les demandes de salaire d'un employé
    public function findSalaryDemands(int $employeeId)
    {
        return $this->createQueryBuilder('e')
            ->select('d')
            ->from('App\Entity\Demand', 'd')
            ->andWhere('d.employe = :employeeId')
            ->andWhere('d.type = :demandType')
            ->andWhere('d.status = :demandStatus') // Ajouter cette condition
            ->setParameter('employeeId', $employeeId)
            ->setParameter('demandType', 'salary') // Assurez-vous que le type est correct
            ->setParameter('demandStatus', true) // Assurez-vous que le statut est 'true'
            ->getQuery()
            ->getResult();
    }



    
    
}