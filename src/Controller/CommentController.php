<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\SnowtrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment', name: 'app_comment_')]
class CommentController extends AbstractController
{
	public function __construct(
		private readonly CommentRepository $commentRepository,
		private readonly SnowtrickRepository $snowtrickRepository,
	) {
	}

	#[Route('/create/{slug}', name: 'create', methods: ['POST'])]
	public function create(string $slug, Request $request): Response
	{
		// Check if user is authenticated
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		$user = $this->getUser();

		$snowtrick = $this->snowtrickRepository->findOneBy(['slug' => $slug]);
		if (null === $snowtrick) {
			$this->addFlash('danger', 'Snowtrick not found.');
			return $this->redirectToRoute('app_home');
		}

		$content = $request->request->get('content');
		if (empty($content)) {
			$this->addFlash('danger', 'Comment cannot be empty.');
			return $this->redirectToRoute('app_snowtrick_show', ['slug' => $slug]);
		}

		$comment = new Comment();
		$comment
			->setContent($content)
			->setSnowtrick($snowtrick)
			->setCreatedBy($user)
			->setCreatedAt(new \DateTimeImmutable())
		;

		$this->commentRepository->save($comment);

		$this->addFlash('success', 'Comment added successfully.');
		return $this->redirectToRoute('app_snowtrick_show', ['slug' => $slug]);
	}

	#[Route('/load-more/{trickId}/{page}', name: 'load_more', methods: ['GET'])]
	public function loadMore(int $trickId, int $page): JsonResponse
	{
		$commentsPerPage = 10;
		$snowtrick = $this->snowtrickRepository->find($trickId);

		if (null === $snowtrick) {
			return new JsonResponse([
				'success' => false,
				'error' => 'Snowtrick not found.',
			], Response::HTTP_NOT_FOUND);
		}

		$comments = $this->commentRepository->findBy(
			['snowtrick' => $snowtrick],
			['createdAt' => 'DESC'],
			$commentsPerPage,
			$page * $commentsPerPage // Skip the comments already displayed (page 2 starts at 20)
		);

		$totalComments = $this->commentRepository->count(['snowtrick' => $snowtrick]);
		$commentsLoaded = $page * $commentsPerPage + count($comments);

		$data = [];
		foreach ($comments as $comment) {
			$data['comments'][] = [
				'id' => $comment->getId(),
				'content' => $comment->getContent(),
				'userEmail' => $comment->getCreatedBy()->getEmail(),
				'createdAt' => $comment->getCreatedAt()->format('d M Y H:i'),
			];
		}

		$data['hasMoreComments'] = $commentsLoaded < $totalComments;

		return new JsonResponse([
			'success' => true,
			'data' => $data,
		], Response::HTTP_OK);
	}
}
