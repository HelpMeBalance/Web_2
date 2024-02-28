<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/article', name: 'admin_article_')]
class AdminArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    //Partie Ajout Article
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/article/index.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
    //Affichage D'un article a partir de son id (show)
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'article' => $article,
        ]);
    }

    //Partie modification a partir du son id
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(int $id, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->find($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        $articles = $articleRepository->findAll();

        return $this->render('admin/article/index.html.twig', [
            'article' => $article,
            'articles' => $articles,
            'form' => $form->createView(),
        ]);
    }

    //Partie delete a partir du son id
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->find($id);

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
