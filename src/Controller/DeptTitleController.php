<?php

namespace App\Controller;

use App\Entity\DeptTitle;
use App\Form\DeptTitleType;
use App\Form\ApplyType;
use App\Repository\DeptTitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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


    #[Route('/apply/{id}', name: 'app_dept_title_apply', methods: ['GET', 'POST'])]
    public function apply(Request $request, DeptTitle $deptTitle, MailerInterface $mailer): Response
    {
        $applyForm = $this->createForm(ApplyType::class);
       
        $applyForm->handleRequest($request);
        $managerEmail = $deptTitle->getDepartment()->getDeptManager()->getEmployee()->getEmail();
    
        if ($applyForm->isSubmitted() && $applyForm->isValid()) {
            $data = $applyForm->getData();
    
            // Gérez le téléchargement de la pièce jointe
            $uploadedFile = $data['cv'];
    
            if ($uploadedFile) {
                // Assurez-vous que le répertoire d'attachements existe
                $attachmentsDirectory = $this->getParameter('attachments_directory');
                if (!file_exists($attachmentsDirectory)) {
                    mkdir($attachmentsDirectory);
                }
    
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
    
                try {
                    $uploadedFile->move($attachmentsDirectory, $newFilename);
                } catch (FileException $e) {
                    // Gérez l'erreur de téléchargement du fichier
                    // Vous pouvez ajouter un message flash ou rediriger avec un message d'erreur
                    $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement de la pièce jointe.');
                    return $this->redirectToRoute('app_dept_title_apply', ['id' => $deptTitle->getId()]);
                }
    
                // Attachez le fichier au message
                $message = (new Email())
                    ->from($data['email'])
                    ->to($managerEmail)
                    ->subject('Nouvelle candidature')
                    ->html(
                        $this->renderView(
                            'dept_title/email.html.twig',
                            [
                                'dept_title' => $deptTitle,
                                'data' => $data,
                            ]
                        )
                    )
                    ->attachFromPath($attachmentsDirectory.'/'.$newFilename); // Attachez le fichier
            }
    
            // Envoyez le message
            $mailer->send($message);
    
            $this->addFlash('success', 'Votre candidature a bien été envoyée');
    
            return $this->redirectToRoute('app_dept_title_index');
        }
    
        return $this->render('dept_title/apply.html.twig', [
            'dept_title' => $deptTitle,
            'apply_form' => $applyForm->createView(),
        ]);
    }
    

}
