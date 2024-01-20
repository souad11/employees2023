<?php

namespace App\Controller;

use App\Entity\Leave;
use App\Form\LeaveType;
use App\Repository\LeaveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/leave')]
class LeaveController extends AbstractController
{
    #[Route('/', name: 'app_leave_index', methods: ['GET'])]
    public function index(LeaveRepository $leaveRepository): Response
    {

        $today = new \DateTime('today');
        $todayLeaves = $leaveRepository->findLeavesByDate($today);

        return $this->render('leave/index.html.twig', [
            'todayLeaves' => $todayLeaves,
        ]);
    }

    #[Route('/new', name: 'app_leave_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $leave = new Leave();
        $form = $this->createForm(LeaveType::class, $leave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($leave);
            $entityManager->flush();

            return $this->redirectToRoute('app_leave_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('leave/new.html.twig', [
            'leave' => $leave,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_leave_show', methods: ['GET'])]
    public function show(Leave $leave): Response
    {
        return $this->render('leave/show.html.twig', [
            'leave' => $leave,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_leave_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Leave $leave, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LeaveType::class, $leave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_leave_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('leave/edit.html.twig', [
            'leave' => $leave,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_leave_delete', methods: ['POST'])]
    public function delete(Request $request, Leave $leave, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$leave->getId(), $request->request->get('_token'))) {
            $entityManager->remove($leave);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_leave_index', [], Response::HTTP_SEE_OTHER);
    }

    //c'est route pour update de la table leave sur le champ to_date après avoir cliquer sur cette route
    // <form action="{{ path('app_leave_return_from_leaves', {'id': leave.id}) }}" method="post">
    //                 <button class="btn btn-danger" type="submit"><i class="fas fa-times"></i></button>
    //             </form>

    #[Route('/{id}/return', name: 'app_leave_return_from_leaves', methods: ['POST'])]
    public function returnFromLeaves(Leave $leave, EntityManagerInterface $entityManager): Response
    {
        $leave->setToDate(new \DateTime('yesterday'));
        $entityManager->flush();

        return $this->redirectToRoute('app_leave_index', [], Response::HTTP_SEE_OTHER);
    }
}
