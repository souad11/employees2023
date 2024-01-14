<?php

namespace App\Controller;

use App\Entity\Demand;
use App\Form\DemandType;
use App\Repository\DemandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/demand')]
class DemandController extends AbstractController
{
    #[Route('/', name: 'app_demand_index', methods: ['GET'])]
    public function index(DemandRepository $demandRepository): Response
    {

        if ($this->denyAccessUnlessGranted('ROLE_ADMIN')) {
            //redirection vers la page de login avec un message flash
            $this->addFlash('danger', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('app_home');
        }
        
        if ($this->denyAccessUnlessGranted('ROLE_USER')) {
            //redirection vers la page de login avec un message flash
            $this->addFlash('danger', 'Vous devez vous connecter pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('demand/index.html.twig', [
            'demands' => $demandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_demand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {        
        if ($this->denyAccessUnlessGranted('ROLE_USER')) {
            //redirection vers la page de login avec un message flash
            $this->addFlash('danger', 'Vous devez vous connecter pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $demand = new Demand();

        // Récupérer l'utilisateur actuel
        $user = $this->getUser();

        // Passer l'utilisateur au formulaire
        $form = $this->createForm(DemandType::class, $demand, [
            'user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demand);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_show', ['id' => $demand->getEmploye()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demand/new.html.twig', [
            'demand' => $demand,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_demand_show', methods: ['GET'])]
    public function show(Demand $demand): Response
    {
        return $this->render('demand/show.html.twig', [
            'demand' => $demand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demand $demand, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(DemandType::class, $demand, [
            'user' => $user,
            'is_edit_mode' => true, // Passer true pour le mode édition
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demand/edit.html.twig', [
            'demand' => $demand,
            'form' => $form,
            
        ]);
    }

    #[Route('/{id}', name: 'app_demand_delete', methods: ['POST'])]
    public function delete(Request $request, Demand $demand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demand->getId(), $request->request->get('_token'))) { 
            $entityManager->remove($demand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/', name: 'app_demand_delete_all', methods: ['POST'])]
    public function deleteAll(DemandRepository $demandRepository, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        if ($this->isCsrfTokenValid('delete_all', $_POST['_token'])) {
        $demands = $demandRepository->findAll();
        
            foreach($demands as $demand){
                if($demand->isStatus() !== null ) {
                    $entityManager->remove($demand);
                    $entityManager->flush();
                }

            }
        }
        
        return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
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
        $demand->setStatus($status == '1' ? 1 : 0);
        
        $entityManager->flush();

        return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);

    }
}
