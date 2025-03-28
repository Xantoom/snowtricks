<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\File;
use App\Entity\Snowtrick;
use App\Enum\SnowtrickCategories;
use App\Repository\FileRepository;
use App\Repository\SnowtrickRepository;
use App\Service\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/snowtrick', name: 'app_snowtrick_')]
class SnowtrickController extends AbstractController
{
	public function __construct(
		private readonly SnowtrickRepository $snowtrickRepository,
		private readonly FileManager $fileManager,
	) {
	}

	#[Route('/{slug}', name: 'show', methods: ['GET'], priority: -1)]
	public function show(string $slug): Response
	{
		$snowtrick = $this->snowtrickRepository->findOneBy(['slug' => $slug]);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_home');
		}

		return $this->render('snowtrick/view.html.twig', [
			'snowtrick' => $snowtrick,
			'defaultBannerImg' => $_ENV['DEFAULT_BANNER_IMAGE'],
		]);
	}

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function create(Request $request): Response
	{
		// Check if user has permission to create
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		// Handle form submission
		if ($request->isMethod('POST')) {
			$title = $request->request->get('title');
			$description = $request->request->get('description');
			$category = $request->request->get('category');

			// Validate inputs
			if (empty($title) || empty($description) || empty($category)) {
				$this->addFlash('danger', 'All fields are required.');
			} else {
				// Create new snowtrick
				$snowtrick = new Snowtrick();
				$snowtrick
					->setName($title)
					->setDescription($description)
					->setCategory(SnowtrickCategories::from($category))
					->setCreatedAt(new \DateTimeImmutable())
					->setCreatedBy($this->getUser())
				;

				// Generate slug from title
				$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
				$snowtrick->setSlug($slug);

				// Save to database
				$this->snowtrickRepository->save($snowtrick);

				$this->addFlash('success', 'Snowtrick created successfully.');
				return $this->redirectToRoute('app_snowtrick_show', ['slug' => $snowtrick->getSlug()]);
			}
		}

		return $this->render('snowtrick/create.html.twig', [
			'defaultBannerImg' => $_ENV['DEFAULT_BANNER_IMAGE'],
			'categories' => SnowtrickCategories::cases(),
		]);
	}

	#[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(string $slug, Request $request): Response
	{
		$snowtrick = $this->snowtrickRepository->findOneBy(['slug' => $slug]);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_home');
		}

		// Check if user has permission to edit
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		// Handle form submission
		if ($request->isMethod('POST')) {
			$title = $request->request->get('title');
			$description = $request->request->get('description');
			$category = $request->request->get('category');

			// Validate inputs
			if (empty($title) || empty($description) || empty($category)) {
				$this->addFlash('danger', 'All fields are required.');
			} else {
				// Update snowtrick
				$snowtrick->setName($title);
				$snowtrick->setDescription($description);
				$snowtrick->setCategory(SnowtrickCategories::from($category));
				$snowtrick->setUpdatedAt(new \DateTimeImmutable());

				// Generate slug from title
				$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
				$snowtrick->setSlug($slug);

				// Save to database
				$this->snowtrickRepository->save($snowtrick);

				$this->addFlash('success', 'Snowtrick updated successfully.');
				return $this->redirectToRoute('app_snowtrick_show', ['slug' => $snowtrick->getSlug()]);
			}
		}

		return $this->render('snowtrick/edit.html.twig', [
			'snowtrick' => $snowtrick,
			'defaultBannerImg' => $_ENV['DEFAULT_BANNER_IMAGE'],
			'categories' => SnowtrickCategories::cases(),
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

		$this->addFlash('success', 'Snowtrick '.$snowtrick->getName().' deleted successfully.');

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

	#[Route('/media/add/{slug}', name: 'add_media', methods: ['POST'])]
	public function addMedia(
		string $slug,
		Request $request,
		FileRepository $fileRepository
	): Response {
		$snowtrick = $this->snowtrickRepository->findOneBy(['slug' => $slug]);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_home');
		}

		// Check if user has permission
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		$user = $this->getUser();

		$mediaType = $request->request->get('mediaType');
		$file = new File();
		$file
			->setType($mediaType)
			->setSnowtrick($snowtrick)
			->setCreatedBy($user)
			->setCreatedAt(new \DateTimeImmutable())
		;

		if ($mediaType === 'image') {
			$imageFile = $request->files->get('imageFile');
			if (!$imageFile) {
				$this->addFlash('danger', 'No image file uploaded.');
				return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
			}

			try {
				$newFilename = $this->fileManager->uploadFile($imageFile, $snowtrick->getId());
				$file->setPath($newFilename);
			} catch (FileException $e) {
				$this->addFlash('danger', 'Error uploading file: ' . $e->getMessage());
				return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
			}
		} else {
			$videoUrl = $request->request->get('videoUrl');
			if (empty($videoUrl)) {
				$this->addFlash('danger', 'Video URL is required.');
				return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
			}
			$file->setPath($videoUrl);
		}

		$fileRepository->save($file);
		$this->addFlash('success', 'Media added successfully.');

		return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
	}

	#[Route('/media/edit/{slug}', name: 'edit_media', methods: ['POST'])]
	public function editMedia(
		string $slug,
		Request $request,
		FileRepository $fileRepository
	): Response {
		$snowtrick = $this->snowtrickRepository->findOneBy(['slug' => $slug]);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_home');
		}

		// Check if user has permission
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		$user = $this->getUser();

		$fileId = $request->request->get('fileId');
		$file = $fileRepository->find($fileId);

		if (!$file || $file->getSnowtrick() !== $snowtrick) {
			$this->addFlash('danger', 'Media file not found.');
			return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
		}

		$file
			->setEditedBy($user)
			->setEditedAt(new \DateTimeImmutable())
		;

		if ($file->getType() === 'image') {
			$imageFile = $request->files->get('imageFile');
			if ($imageFile) {
				try {
					$newFilename = $this->fileManager->replaceFile($imageFile, $file->getPath(), $snowtrick->getId());
					$file->setPath($newFilename);
				} catch (FileException $e) {
					$this->addFlash('danger', 'Error uploading file: ' . $e->getMessage());
					return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
				}
			}
		} else {
			$videoUrl = $request->request->get('videoUrl');
			if (!empty($videoUrl)) {
				$file->setPath($videoUrl);
			}
		}

		$fileRepository->save($file);
		$this->addFlash('success', 'Media updated successfully.');

		return $this->redirectToRoute('app_snowtrick_edit', ['slug' => $slug]);
	}

	#[Route('/media/delete/{id}', name: 'delete_media', methods: ['DELETE'])]
	public function deleteMedia(
		int $id,
		FileRepository $fileRepository
	): JsonResponse {
		// Check if user has permission
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		$file = $fileRepository->find($id);
		if (!$file) {
			return new JsonResponse([
				'success' => false,
				'error' => 'Media not found',
			], Response::HTTP_NOT_FOUND);
		}

		// Delete the physical file if it's an image
		if ($file->getType() === 'image') {
			$this->fileManager->deleteFile($file->getPath());
		}

		$fileRepository->remove($file);

		return new JsonResponse([
			'success' => true,
		]);
	}
}
