<?php

namespace App\Controller;

use App\Entity\Salary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;

class SalaryController extends AbstractController
{
    #[Route('/salary', name: 'app_salary')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAllEmployeesWithSalaries();

        return $this->render('salary/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/salary/{id}', name: 'app_salary_show')]
    public function show(EmployeeRepository $employeeRepository, int $id): Response
    {
        $employee = $employeeRepository->findEmployeeWithSalaries($id);

        return $this->render('salary/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    //route pour créer un nouveau salaire pour un employé si la demande de salaire est acceptée
    #[Route('/salary/{id}/new', name: 'app_salary_new')]
    public function newSalary(EmployeeRepository $employeeRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        $employee = $employeeRepository->findEmployeeWithSalaries($id);

        $employee = $employee[0];

        //obtenir le dernier salaire avec la de fin de contrat à 9999-01-01

        $lastSalary = $employeeRepository->findLastSalary($id);

        //modifier la date de fin de contrat du dernier salaire

        $lastSalary->setToDate(new \DateTime('now'));

        //obtenir le nouveau salaire (ancien salire + montant de la demande de salaire(demand.getAbout()))
        

        $salaryDemands = $employeeRepository->findSalaryDemands($id);

        //convertir le montant de la demande en float

        var_dump($salaryDemands);die;
        $amountToAdd = (float)$salaryDemands[0]->getAbout();

        

        $newSalaryAmount = $lastSalary->getSalary() + $amountToAdd;


        //supprimer la demande de salaire apprès qu

        $entityManager->remove($salaryDemands[0]);
        

        //créer un nouveau salaire avec la date de début de contrat à aujourd'hui et la date de fin de contrat à 9999-01-01
        
        $newSalary = new Salary();

        $newSalary->setEmployee($employee);
        $newSalary->setSalary($newSalaryAmount);
        $newSalary->setFromDate(new \DateTime('now'));
        $newSalary->setToDate(new \DateTime('9999-01-01'));

        $entityManager->persist($newSalary);
        $entityManager->flush();

        $this->addFlash('success', 'Le nouveau salaire a été créé avec succès.');

        return $this->redirectToRoute('app_salary_show', ['id' => $id]);

    }
}
