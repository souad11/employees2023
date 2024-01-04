<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use App\Repository\DeptEmpRepository;
use App\Repository\DeptManagerRepository;
use App\Repository\DeptTitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/department')]
class DepartmentController extends AbstractController
{
    #[Route('/', name: 'app_department_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em,DepartmentRepository $departmentRepository, DeptManagerRepository $deptManagerRepository,DeptTitleRepository $deptTitleRepository ): Response
    {
        // Sélectionner tous les employés d'un département donné 
        $conn = $em->getConnection();
        //le nombre d'employés d'un departement donné
        $sql = 'SELECT departments.dept_name, COUNT(dept_emp.emp_no) AS nb_employees FROM departments INNER JOIN dept_emp ON departments.dept_no = dept_emp.dept_no GROUP BY departments.dept_name';
        $stmt = $conn->executeQuery($sql);
        $nbEmployees = $stmt->fetchAllAssociative();


        
        

        return $this->render('department/index.html.twig', [
            'departments' => $departmentRepository->findAll(),
            'deptManagers' => $deptManagerRepository->findAll(),
            'deptTitles' => $deptTitleRepository->findAll(),
            'nbEmployees' => $nbEmployees,
            
        ]);
    }

    #[Route('/new', name: 'app_department_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        $conn = $entityManager->getConnection();
        $sql = 'SELECT dept_no FROM departments ORDER BY dept_no DESC LIMIT 1';

        $stmt = $conn->executeQuery($sql);
        $lastDepartmentId = $stmt->fetchOne();

        // Extraire les chiffres après le "d"
        $lastNumber = substr($lastDepartmentId, 1);

        // Incrémenter le nombre
        $newNumber = $lastNumber + 1;

        // Reformater l'identifiant avec le nouveau nombre
        $newDepartmentId = 'd' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $department->setId($newDepartmentId);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('department/new.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_department_show', methods: ['GET'])]
    public function show(Department $department,DeptManagerRepository $deptManagerRepository): Response
    {
        return $this->render('department/show.html.twig', [
            'department' => $department,
            'deptManager' => $deptManagerRepository->findOneBy(['departement' => $department]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_department_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_department_delete', methods: ['POST'])]
    public function delete(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            $entityManager->remove($department);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
    }
}
