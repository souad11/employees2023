<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Link;
use App\Repository\LinkRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

class HomeController extends AbstractController
{

    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, LinkRepository $linkRepository): Response
    {   
        $linksHome = $linkRepository->findAll();
        $linkDetails = [];
        foreach($linksHome as $link) {
            $id = $link->getId();
            $linkDetails[$id] = $link;

        }

        return $this->render('home/index.html.twig', [
            'title' => 'Home',
            'linkDetails' => $linkDetails,
        ]);
    }

    #[Route('/download-pdf/{linkId}', name: 'download_pdf')]
    public function generatePdfAction(LinkRepository $linkRepository, $linkId): Response
    {

        $link = $linkRepository->find($linkId); 

        if (!$link) {
            throw $this->createNotFoundException('Lien non trouvé');
        }

        $url = $link->getUrl();
        $content = file_get_contents($url);
        if ($content === false) {
            throw new \Exception("Impossible de récupérer le contenu depuis l'URL.");
        }
        $content = mb_convert_encoding($content, 'UTF-8');

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($content);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response($dompdf->output());
  
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="telechargement.pdf"');

        return $response;
    }

}
