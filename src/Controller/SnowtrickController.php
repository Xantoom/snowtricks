<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SnowtrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/snowtrick', name: 'app_snowtrick_')]
class SnowtrickController extends AbstractController
{
	public function __construct(
		private readonly SnowtrickRepository $snowtrickRepository,
	) {
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(): Response
	{
		return $this->render('snowtrick/index.html.twig');
	}

	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(int $id): Response
	{
		$snowtrick = $this->snowtrickRepository->find($id);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_snowtrick_index');
		}

		return $this->render('snowtrick/view.html.twig', [
			'snowtrick' => $snowtrick,
		]);
	}

	#[Route('/create', name: 'create', methods: ['POST'])]
	public function create(Request $request): Response
	{
		return $this->render('snowtrick/create.html.twig');
	}

	#[Route('/edit/{id}', name: 'edit', methods: ['PATCH'])]
	public function edit(int $id, Request $request): Response
	{
		$snowtrick = $this->snowtrickRepository->find($id);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_snowtrick_index');
		}

		return $this->render('snowtrick/edit.html.twig', [
			'snowtrick' => $snowtrick,
		]);
	}

	#[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
	public function delete(int $id): JsonResponse
	{
		$snowtrick = $this->snowtrickRepository->find($id);
		if (null === $snowtrick) {
			return new JsonResponse([
				'error' => 'Snowtrick not found.',
			], Response::HTTP_NOT_FOUND);
		}

		$this->snowtrickRepository->remove($snowtrick);

		return new JsonResponse(null, Response::HTTP_OK);
	}

	#[Route('/load-more/{page}', name: 'load_more', methods: ['GET'])]
	public function loadMore(int $page): JsonResponse
	{
		$tricksPerPage = 15;
		$tricks = $this->snowtrickRepository->findBy(
			[],
			['createdAt' => 'DESC'],
			$tricksPerPage,
			($page - 1) * $tricksPerPage
		);
		$totalNbTricks = $this->snowtrickRepository->count([]);

		$data = [];
		foreach ($tricks as $trick) {
			$data['tricks'][] = [
				'id' => $trick->getId(),
				'name' => $trick->getName(),
			];
		}
		$data['isThereAnyTricksLeftToDisplay'] = $totalNbTricks / $tricksPerPage > $page;
		$data['isCurrentUserLoggedIn'] = $this->getUser() !== null;

		return new JsonResponse([
			'success' => true,
			'data' => $data,
		], Response::HTTP_OK);
	}
}
