<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\DemandRepository;


#[Route('/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
       
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            // gestion de l'identifiant
            $conn = $entityManager->getConnection();
            $sql = 'SELECT emp_no FROM employees ORDER BY emp_no DESC LIMIT 1';
            $stmt = $conn->executeQuery($sql);
            $lastEmployeeId = $stmt->fetchOne();
            // var_dump($lastEmployeeId);
            $employee->setId($lastEmployeeId + 1);
            // var_dump($employee->getId());

            //gestion de la photo

            $photo = $form->get('photo')->getData();
            // var_dump($photo);die;

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // var_dump($originalFilename);die;
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                // var_dump($newFilename);die;
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );
                $employee->setPhoto($newFilename);
            }
            
            $entityManager->persist($employee);
            

            $entityManager->flush();
            

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        //photo du manager du departement


        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //gestion de la photo, écrasement de l'ancienne photo

            $photo = $form->get('photo')->getData();
            // var_dump($photo);die;

            if ($photo) {
                //suppression de l'ancienne photo
                $oldPhoto = $employee->getPhoto();
                if ($oldPhoto) {
                    unlink($this->getParameter('photo_directory') . '/' . $oldPhoto);
                }

                //ajout de la nouvelle photo
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // var_dump($originalFilename);die;
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                // var_dump($newFilename);die;
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );
                $employee->setPhoto($newFilename);
            }


            $entityManager->flush();

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/{status}', name:'app_demand_update_status', methods: ['POST'])]
    public function updateDemandStatus(DemandRepository $demandRepository, EntityManagerInterface $entityManager, $id, $status): Response
    {
        $demand = $demandRepository->find($id);

        if(!$demand){
            throw $this->createNotFoundException('Demande introuvable');
        }
        

        
        if($status == '1'){
            $demand->setStatus(1);
            
        }elseif($status == '0'){
            $demand->setStatus(0);
        }
        
        $entityManager->flush();

        return $this->redirectToRoute('app_employee_show', ['id' => $demand->getEmploye()->getId()], Response::HTTP_SEE_OTHER);

    }
}
