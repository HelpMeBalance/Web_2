<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Entity\CategorieProduit;
use App\Repository\CategorieProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_homeClient')]
    public function index(UserRepository $Urep, Request $request, EntityManagerInterface $entityManager): Response
    {

        $rendezVou = new RendezVous();
        $rendezVou->setDateR(new \DateTime());
        // $form = $this->createForm(RendezVousType::class, $rendezVou, ['patient' => $Urep->find($idp)]);
        $form = $this->createForm(RendezVousType::class, $rendezVou, ['patient' => $this->getUser()]);
        $form->handleRequest($request);
        $errordate = "";

        if ($form->isSubmitted() && $form->isValid()) {

            $rendezVou->setStatut(false);
            //$rendezVou->setPatient($Urep->find(?????????));
            $entityManager->persist($rendezVou);
            $entityManager->flush();
            // Check if the selected date is before today
            if ($rendezVou->getDateR() < new \DateTime('today')) {
                $form->get('dateR')->addError(new \Symfony\Component\Form\FormError('Please select a date equal to or after today.'));
                $errordate = "Please select a date equal to or after today.";
                return $this->render('rendez_vous/new.html.twig', [
                    'rendez_vou' => $rendezVou,
                    'form' => $form->createView(),
                    'errordate' => $errordate,
                ]);
            }

            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontClient/index.html.twig', [
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 1,
            'title' => '',
            'titlepage' => '',
            'form' => $form->createView(),
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
    public function shopClient(CategorieProduitRepository $categorieProduitRepository, Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
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


        return $this->render('frontClient/shop.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categorie_produits' => $categorieProduitRepository->findAll(),
            'controller_name' => 'HomeController',
            'service' => 0,
            'part' => 3,
            'title' => 'Store',
            'titlepage' => 'Store- ',
            'articles' => $articles,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
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
            throw $this->createNotFoundException('No article found for id ' . $id);
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
