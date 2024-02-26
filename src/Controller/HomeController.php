<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Entity\CategorieProduit;
use App\Repository\CategorieProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homeClient')]
    public function index(): Response
    {
        return $this->render('frontClient/index.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 1,
            'title' => '',
            'titlepage' => '',
        ]);
    }
    #[Route('/aboutClient', name: 'app_aboutClient')]
    public function aboutClient(): Response
    {
        return $this->render('frontClient/about.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 2,
            'title' => 'Who Are We',
            'titlepage' => 'About- ',
        ]);
    }



    // 3:shop 
    #[Route('/shopClient', name: 'app_shopClient')]
    public function shopClient(ArticleRepository $articleRepository, CategorieProduitRepository $categorieProduitRepository): Response
    {
        return $this->render('frontClient/shop.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categorie_produits' => $categorieProduitRepository->findAll(),
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 3,
            'title' => 'Store',
            'titlepage' => 'Store- ',
        ]);
    }

    // shop : Affichage catégorie 
    #[Route('/shopClient/categrie/produit/{id}', name: 'app_categorie_produit_show', methods: ['GET'])]
    public function showcatprod($id, ArticleRepository $articleRepository, CategorieProduitRepository $categorieProduitRepository): Response
    {
        $categorieProduit = $categorieProduitRepository->find($id);
        if (!$categorieProduit) {
            throw $this->createNotFoundException('Category not found');
        }

        // Get articles for the specified category
        $articles = $articleRepository->findBy(['categorie' => $categorieProduit]);

        return $this->render('frontClient/articlesparCat.html.twig', [
            'categorie_produits' => $categorieProduitRepository->findAll(),
            'articles' => $articles,
            'service' => 0,
            'part' => 3,
            'title' => 'Store',
            'titlepage' => 'Store - ',
        ]);
    }

    //Affichage D'un article a partir de son id (show)
    #[Route('/shopClient/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(int $id, ArticleRepository $articleRepository, CategorieProduitRepository $categorieProduitRepository): Response
    {
    $article = $articleRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('No article found for id '.$id);
    }

        return $this->render('frontClient/articles_details.html.twig', [
            'article' => $article,
            'categorie_produits' => $categorieProduitRepository->findAll(),
            'service' => 0,
            'part' => 3,
            'title' => 'Articles',
            'titlepage' => 'Articles- ',
        ]);
    }



    #[Route('/serviceClient', name: 'app_serviceClient')]
    public function serviceClient(): Response
    {
        return $this->render('frontClient/service.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 1,
            'part' => 4,
            'title' => 'Services',
            'titlepage' => 'Services- ',
        ]);
    }
    //5:blog
    #[Route('/faqClient', name: 'app_faqClient')]
    public function faqClient(): Response
    {
        return $this->render('frontClient/faq.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 1,
            'part' => 6,
            'title' => 'Questions Corner',
            'titlepage' => 'FAQ- ',
        ]);
    }
    #[Route('/contactClient', name: 'app_contactClient')]
    public function contactClient(): Response
    {
        return $this->render('frontClient/contact.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 7,
            'title' => 'Contact Us',
            'titlepage' => 'Contact- ',
        ]);
    }

    ///////////////ADMIN PART/////////////////////////
    #[Route('/homeAdmin', name: 'app_homeAdmin')]
    public function indexAdmin(): Response
    {
        return $this->render('frontAdmin/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
