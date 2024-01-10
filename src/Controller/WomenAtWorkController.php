<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employee;
use App\Entity\DeptEmp;
use App\Entity\Department;

class WomenAtWorkController extends AbstractController
{
    #[Route('/women/at/work', name: 'app_women_at_work')]
    public function stat(EntityManagerInterface $entityManager): Response
    {   

        $queryBis = $entityManager->createQuery('
            SELECT 
                e.hireDate as hireDate,
                d.deptName as deptName,
                COUNT(e.id) as totalEmployees,
                SUM(CASE WHEN e.gender = :female THEN 1 ELSE 0 END) as femaleCount,
                SUM(CASE WHEN e.gender = :male THEN 1 ELSE 0 END) as maleCount
            FROM 
                App\Entity\Employee e
                JOIN App\Entity\DeptEmp de WITH e.id = de.employee
                JOIN App\Entity\Department d WITH de.department = d.id
            WHERE 
                e.gender IN (:female, :male)
            GROUP BY 
                d.deptName , e.hireDate 
        ');

        $queryBis->setParameter('female', 'F');
        $queryBis->setParameter('male', 'M');
        $resultsBis = $queryBis->getResult();


        // $query = $entityManager->createQueryBuilder()
        //     ->select('e.hireDate as hireDate', 'd.deptName as deptName','COUNT(e.id) as totalEmployees', 'SUM(CASE WHEN e.gender = :female THEN 1 ELSE 0 END) as femaleCount', 'SUM(CASE WHEN e.gender = :male THEN 1 ELSE 0 END) as maleCount')
        //     ->from(Employee::class, 'e')
        //     ->join(DeptEmp::class, 'de', 'WITH', 'e.id = de.empNo')
        //     ->join(Department::class, 'd', 'WITH', 'de.deptNo = d.id')
        //     ->where('e.gender IN (:female, :male)')
        //     ->setParameter('female', 'F')
        //     ->setParameter('male', 'M')
        //     ->groupBy( 'e.hireDate','d.deptName')
        //     ->getQuery()
        //     ->getResult();

        $dataByYearAndDept = [];
        

        foreach ($resultsBis as $row) {
            $deptName = $row['deptName'];
            $year = $row['hireDate']->format('Y');
        
            if (!isset($dataByYearAndDept[$deptName][$year])) {
                $dataByYearAndDept[$deptName][$year] = [
                    "hommes" => 0,
                    "femmes" => 0,
                    "total" => 0,
                ];
            }
            $dataByYearAndDept[$deptName][$year]['hommes'] += $row['maleCount'];
            $dataByYearAndDept[$deptName][$year]['femmes'] += $row['femaleCount'];
            $dataByYearAndDept[$deptName][$year]['total'] += $row['totalEmployees'];
        }
        
        $datesEngagement = [];
        foreach ($dataByYearAndDept as $deptName => $dataByYear) {
            foreach ($dataByYear as $year => $counts) {
                $datesEngagement[$deptName][$year] = [
                    "hommes" => $counts['hommes'],
                    "femmes" => $counts['femmes'],
                    "total" => $counts['total'],
                ];
            }
        } 
        

        // 3 departements qui ont le plus/moins de femmes 

        $dataByDepartment = [];
        foreach ($resultsBis as $row) { // $query
            $deptName = $row['deptName'];
            $totalFemale = $row['femaleCount'];

            if (!isset($dataByDepartment[$deptName])) {
                $dataByDepartment[$deptName] = [
                    "totalFemale" => 0,
                ];
            }

            $dataByDepartment[$deptName]['totalFemale'] += $totalFemale;
        }

        uasort($dataByDepartment, function ($a, $b) {
            return $b['totalFemale'] - $a['totalFemale'];
        });

        $topThreeDepartments = array_slice($dataByDepartment, 0, 3);

        uasort($dataByDepartment, function ($a, $b) {
            return $a['totalFemale'] - $b['totalFemale'];
        });
        
        $bottomThreeDepartments = array_slice($dataByDepartment, 0, 3);

        // Nombre de femmes manager

        $queryManagerWoman = $entityManager->createQuery('
        SELECT COUNT(DISTINCT e.id) AS totalFemaleManagers
        FROM App\Entity\DeptManager m
        JOIN App\Entity\Employee e WITH m.employee = e.id
        WHERE e.gender = :female
        ');
    
        $queryManagerWoman->setParameter('female', 'F');
        // GetSingleScalarResult retourne 1 seule valeur sinon getResult()
        $countWOmenManager = $queryManagerWoman->getSingleScalarResult(); 

        return $this->render('women_at_work/index.html.twig', [
            'title' => 'Women at work',
            'datesEngagement'=> $datesEngagement,
            'countWOmenManager'=> $countWOmenManager,
            'topThreeDepartments'=> $topThreeDepartments,
            'bottomThreeDepartments'=> $bottomThreeDepartments,
            // 'countBis'=> var_dump($resultsBis),

        ]);
    }
    
}
