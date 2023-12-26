<?php

namespace App\Controller;

use App\Entity\EmpTitle;
use App\Form\EmpTitleType;
use App\Repository\EmpTitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/emp/title')]
class EmpTitleController extends AbstractController
{
    #[Route('/', name: 'app_emp_title_index', methods: ['GET'])]
    public function index(EmpTitleRepository $empTitleRepository): Response
    {
        return $this->render('emp_title/index.html.twig', [
            'emp_titles' => $empTitleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_emp_title_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $empTitle = new EmpTitle();
        $form = $this->createForm(EmpTitleType::class, $empTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($empTitle);
            $entityManager->flush();

            return $this->redirectToRoute('app_emp_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emp_title/new.html.twig', [
            'emp_title' => $empTitle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emp_title_show', methods: ['GET'])]
    public function show(EmpTitle $empTitle): Response
    {
        return $this->render('emp_title/show.html.twig', [
            'emp_title' => $empTitle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emp_title_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EmpTitle $empTitle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmpTitleType::class, $empTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_emp_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emp_title/edit.html.twig', [
            'emp_title' => $empTitle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emp_title_delete', methods: ['POST'])]
    public function delete(Request $request, EmpTitle $empTitle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$empTitle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($empTitle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_emp_title_index', [], Response::HTTP_SEE_OTHER);
    }
}
