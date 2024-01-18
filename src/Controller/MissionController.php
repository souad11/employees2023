<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mission')]
class MissionController extends AbstractController
{
    #[Route('/', name: 'app_mission_index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository): Response
    {
        return $this->render('mission/index.html.twig', [
            'missions' => $missionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mission);
            $entityManager->flush();

            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mission_show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mission_delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/employee/{id}', name: 'app_mission_my_missions', methods: ['GET'])]

    public function myMissions(Employee $employee, MissionRepository $missionRepository): Response
    {

        $notTakenMissions = $missionRepository->findBy(['status' => 'not taken']);


        // dd($employee->getMissions()->toArray());

        $unFinishedMissions = $missionRepository->findUnfinishedMissionsByEmployee($employee);

        // var_dump($missions);die;
        
        return $this->render('mission/show_my_missions.html.twig', [
            'missions' => $unFinishedMissions, 
            'employee' => $employee,
            'notTakenMissions' => $notTakenMissions
        ]);
    }
    
    #[Route('/{id}/{status}', name: 'app_mission_my_missions_update_status', methods: ['POST'])]
    public function updateStatus(Mission $mission, string $status, EntityManagerInterface $entityManager, MissionRepository $missionRepository): Response
    {
        
    if ($this->denyAccessUnlessGranted('ROLE_USER')) {
         //redirection vers la page de login avec un message flash
         $this->addFlash('danger', 'Vous devez vous connecter pour exécuter cette action.');
        return $this->redirectToRoute('app_login');
    }

    /** @var Employee $employee */
    $employee = $this->getUser();

    if ($status === 'ongoing') {
        $ongoingMissionsCount = $missionRepository->countOngoingMissionsForEmployee($employee);

        if ($ongoingMissionsCount === 0) {
            // Ajoute la mission à la table emp_mission
            $employee->addMission($mission);
            $mission->setStatus($status);
            $this->addFlash('success', 'La mission a été acceptée avec succès.');
        } else {
            $this->addFlash('danger', 'Vous avez déjà une mission en cours.');
        }
    } elseif ($status === 'done') {

        $employee->removeMission($mission);
        $mission->setStatus($status);
        $this->addFlash('success', 'La mission a été terminée avec succès.');
    }

    $entityManager->flush();

    return $this->redirectToRoute('app_mission_my_missions', ['id' => $employee->getId()]);
    }


    




        


        
}
