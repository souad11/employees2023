<?php

namespace App\Controller;

use App\Form\OffreEmploiType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class OffreEmploiController extends AbstractController
{
    #[Route('/offre/emploi', name: 'app_offre_emploi', methods: ['GET', 'POST'])]
    public function form(Request $request): Response
    {
        $departmentId = $request->query->get('id');

        $form = $this->createForm(OffreEmploiType::class);
        return $this->render('offre_emploi/_form.html.twig', [
            'form' => $form,
            'departmentId' => $departmentId,
        ]);
        
    }
}
