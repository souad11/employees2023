<?php

namespace App\Controller;

use App\Entity\DeptTitle;
use App\Form\DeptTitleType;
use App\Repository\DeptTitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dept/title')]
class DeptTitleController extends AbstractController
{
    #[Route('/', name: 'app_dept_title_index', methods: ['GET'])]
    public function index(DeptTitleRepository $deptTitleRepository): Response
    {
        return $this->render('dept_title/index.html.twig', [
            'dept_titles' => $deptTitleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dept_title_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deptTitle = new DeptTitle();
        $form = $this->createForm(DeptTitleType::class, $deptTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deptTitle);
            $entityManager->flush();

            return $this->redirectToRoute('app_dept_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dept_title/new.html.twig', [
            'dept_title' => $deptTitle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dept_title_show', methods: ['GET'])]
    public function show(DeptTitle $deptTitle): Response
    {
        return $this->render('dept_title/show.html.twig', [
            'dept_title' => $deptTitle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dept_title_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DeptTitle $deptTitle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DeptTitleType::class, $deptTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dept_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dept_title/edit.html.twig', [
            'dept_title' => $deptTitle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dept_title_delete', methods: ['POST'])]
    public function delete(Request $request, DeptTitle $deptTitle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deptTitle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($deptTitle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dept_title_index', [], Response::HTTP_SEE_OTHER);
    }
}
