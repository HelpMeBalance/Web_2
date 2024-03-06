<?php
namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\ArticleRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\equalTo;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        // Fetch panier items
        $paniers = $panierRepository->findAll();
    
        // Calculate total sum of prices
        $totalSum = 0;
        foreach ($paniers as $panier) {
            $totalSum += $panier->getPrixTot();
        }
    
        // Pass data to the Twig template
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'totalSum' => $totalSum,
            'title' => 'Panier',
            'titlepage' => 'Panier',
            'controller_name' => 'PanierController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PanierRepository $Prep): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //test
            $paniers = $Prep->findAll();
            $exist = false;
            $p = new Panier();
            foreach ($paniers as $p) {
                if ($p->getArticle()->getNom()==$panier->getArticle()->getNom()) {
                    $p->setQuantity($panier->getQuantity());
                    $exist = true;
                }
            }
            if ($exist == true) {
                $entityManager->persist($p);
                $p->setPrixTot($p->getQuantity() * $p->getArticle()->getPrix());
                $entityManager->flush();
            }
            //end test
            else {
                $panier->setPrixTot($panier->getQuantity() * $panier->getArticle()->getPrix());
                $entityManager->persist($panier);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'title' => 'Panier',
            'titlepage' => 'Panier',
            'controller_name' => 'PanierController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'title' => 'Panier',
            'titlepage' => 'Panier',
            'controller_name' => 'PanierController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_panier_delete')]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($panier);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/{idA}', name: 'app_panier_add', methods: ['GET', 'POST'])]
public function add(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository, ArticleRepository $articleRepository, $idA): Response
{
    // Find the article by its ID
    $article = $articleRepository->find($idA);

    // If the article is not found, redirect with an error message
    if (!$article) {
        $this->addFlash('error', 'Article not found.');
        return $this->redirectToRoute('app_shopClient');
    }

    // Check if the user has an existing panier item for this article
    $existingPanierItem = $panierRepository->findOneBy(['article' => $article, 'user' => $this->getUser()]);

    if ($existingPanierItem) {
        // If an existing panier item is found, update its quantity and total price
        $existingPanierItem->setQuantity($existingPanierItem->getQuantity() + 1);
        $existingPanierItem->setPrixTot($existingPanierItem->getQuantity() * $existingPanierItem->getArticle()->getPrix());
    } else {
        // If no existing panier item is found, create a new one
        $panier = new Panier();
        $panier->setUser($this->getUser());
        $panier->setArticle($article);
        $panier->setQuantity(1);
        $panier->setPrixTot($article->getPrix());
        
        $entityManager->persist($panier);
    }

    // Flush changes to the database
    $entityManager->flush();

    // Redirect to the shop client page
    return $this->redirectToRoute('app_shopClient');
}
}