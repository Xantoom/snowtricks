<?php

namespace App\Controller;

use App\Repository\SnowtrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
		SnowtrickRepository $snowtrickRepository
    ): Response
    {
		$this->addFlash('success', 'Bienvenue sur SnowTricks !');

		$maxNbTricks = 15;
		$tricks = $snowtrickRepository->findBy([], ['createdAt' => 'DESC'], $maxNbTricks);
		$nbTricks = $snowtrickRepository->count([]);

        return $this->render('home/index.html.twig', [
			'tricks' => $tricks,
			'nbTricks' => $nbTricks,
	        'bannerImgLink' => 'https://images4.alphacoders.com/598/598152.jpg',
        ]);
    }
}
