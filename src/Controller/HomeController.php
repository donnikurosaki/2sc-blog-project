<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $artRepo, Request $request, UserPasswordHasherInterface $passHasher, EntityManagerInterface $entityManager): Response
    {
        // $user = new User();
        // $user->setUsername('Administrateur')
        //     ->setPassword($passHasher->hashPassword($user, '@dmin2csBlog'))
        //     ->setRoles(['ROLE_ADMIN']);
        // $entityManager->persist($user);
        // $entityManager->flush();

        $page= $request->query->getInt('page', 1);
        $limit= 6;
        $latest_articles = $artRepo->findBy( [], [ 'id' => 'DESC' ], 4);

        $articles = $artRepo
            ->paginatorArticles($page, $limit);

        $totalPage = ceil( $articles->getTotalItemCount() / $limit );

        return $this->render('home/home.html.twig', [
            'articles' => $articles,
            'totalPage' => $totalPage,
            'page' => $page,
            'latest_articles' => $latest_articles
        ]);
    }

    #[Route('/show/{slug}-{id}', name: 'app_show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+' ])]
    public function show(ArticleRepository $artRepo, Request $request,int $id, string $slug, Article $article, MailerInterface $mailer) {

        $latest_article = $artRepo->findOneBy(['id' => $id]);

        $comment = new Comment($article);
        $commentForm = $this->createForm(CommentType::class, $comment);


        return $this->render('home/show.html.twig', [
            'slug' => $slug,
            'id' => $id,
            'latest_article' => $latest_article,
            'commentForm' => $commentForm
        ]);
    }

    #[Route('/show_all', name: 'app_showall')]
    public function show_all(ArticleRepository $artRepo) {

        $articles = $artRepo->findAll([],['id' => 'DESC']);

        return $this->render('home/show_all.html.twig',[
            'articles' => $articles,
        ]);
    }

}