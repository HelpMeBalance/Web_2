<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\CategorieProduit;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/admin/article', name: 'admin_article_')]
class AdminArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository, CategorieProduitRepository $Crep): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'nom');
        $sortOrder = $request->query->get('order', 'asc');
        $perPage = 2;

        $currentPage = (int) $request->query->get('page', 1);

        $articles = $this->entityManager->getRepository(Article::class)->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
        $totalArticles = count($articles);
        $totalPages = ceil($totalArticles / $perPage);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            "articleStat" => $articleRepository->findAll(),
            'catigories' => $Crep->findAll(),
            'form' => $form->createView()
        ]);
    }

    //Partie Ajout Article
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $articlePictureFile = $form->get('articlePictureFile')->getData();
            if ($articlePictureFile) {
                $originalFilename = pathinfo($articlePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $articlePictureFile->guessExtension();

                $articlePictureFile->move(
                    $params->get('article_pictures_directory'),
                    $newFilename
                );

                $article->setArticlePicture($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('message', 'Profile updated successfully.');
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(int $id, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager, CategorieProduitRepository $Crep): Response
    {
        $article = $articleRepository->find($id);
        $formUpdate = $this->createForm(ArticleType::class, $article);
        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        $articleAdd = new Article();
        $form = $this->createForm(ArticleType::class, $articleAdd);
        $form->handleRequest($request);
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'nom');
        $sortOrder = $request->query->get('order', 'asc');
        $perPage = 2;

        $currentPage = (int) $request->query->get('page', 1);

        $articles = $this->entityManager->getRepository(Article::class)->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
        $totalArticles = count($articles);
        $totalPages = ceil($totalArticles / $perPage);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($articleAdd);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        

        return $this->render('admin/article/index.html.twig', [
            'article' => $article,
            'articles' => $articles,
            'formUpdate' => $formUpdate->createView(),
            'form' => $form->createView(),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            "articleStat" => $articleRepository->findAll(),
            'catigories' => $Crep->findAll(),
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
