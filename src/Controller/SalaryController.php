<?php

namespace App\Controller;

use App\Entity\Salary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeeRepository;

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
}
