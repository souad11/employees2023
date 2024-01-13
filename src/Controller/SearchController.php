<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(EmployeeRepository $employeeRepository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('searchTerm');
        $employees = [];
    
        if (!empty($searchTerm)) {
            $query = $entityManager->createQuery(
                'SELECT e FROM App\Entity\Employee e 
                WHERE e.firstName LIKE :searchTerm 
                OR e.lastName LIKE :searchTerm'
            )->setParameter('searchTerm', '%' . $searchTerm . '%');
    
            $employees = $query->getResult();
        } else {
            $employees = $employeeRepository->findAll();
        }
    
 
        $pagination = $paginator->paginate(
            $employees, 
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
            'pagination' => $pagination,
        ]);
    }
}
