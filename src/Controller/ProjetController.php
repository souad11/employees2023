<?php

namespace App\Controller;

use App\Entity\EmpProjet;
use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmpProjetRepository;

#[Route('/projet')]
class ProjetController extends AbstractController
{   

    

    #[Route('/', name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository, EmpProjetRepository $empProjetRepository): Response
    {

        if ($this->denyAccessUnlessGranted('ROLE_USER')) {
            //redirection vers la page de login avec un message flash
            $this->addFlash('danger', 'Vous devez vous connecter pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $projets = $projetRepository->findAll();
        $empProjets = $empProjetRepository->findAll();
    
        
        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
            'empProjets' => $empProjets,

        ]);
            
    }

    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }

    //fonction pour DELETE FROM `emp_project` WHERE `emp_no` = 10001 AND `project_id` = 1;
    #[Route('/{id}/delete', name: 'app_projet_delete_emp', methods: ['POST'])]
    
    public function deleteEmp(Request $request, EmpProjet $empProjet, EntityManagerInterface $entityManager): Response
    {
        if ($this->denyAccessUnlessGranted('ROLE_USER')) {
            //redirection vers la page de login avec un message flash
            $this->addFlash('danger', 'Vous devez vous connecter pour exécuter cette action.');
           return $this->redirectToRoute('app_login');
       }

        if ($this->isCsrfTokenValid('delete'.$empProjet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($empProjet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
