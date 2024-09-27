<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/add', name: 'comment_add')]
    public function add(Request $request, ArticleRepository $artRepo,CommentRepository $commentRepo ,EntityManagerInterface $entityManagerInt): Response
    {
        $commentData = $request->request->all('comment');

        if ( !$this->isCsrfTokenValid('comment-add', $commentData['_token']) ) {
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'
            ], Response::HTTP_BAD_REQUEST );
        }

        $article = $artRepo->findOneBy( [ 'id' => $commentData['article'] ] ); 

        if(!$article){
            return $this->json([
                'code' => 'ARTICLE_NOT_FOUND'
            ], Response::HTTP_BAD_REQUEST );
        }

        $comment =new Comment($article);
        $comment->setAuthor($commentData['author']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $entityManagerInt->persist($comment);
        $entityManagerInt->flush($comment);

        $html = $this->renderView('/comment/index.html.twig', [
            'comment' => $comment
        ]);

        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESSFULLY',
            'message' => $html,
            'number_of_comment' => $commentRepo->count(['article' => $article])
        ]);
    }
}
