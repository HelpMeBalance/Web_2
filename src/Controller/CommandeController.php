<?php
// CommandeController.php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\Charge;
use Stripe\Stripe;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        // Fetch commandes associated with the authenticated user
        $commandes = $commandeRepository->findBy(['user' => $this->getUser()]);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Fetch paniers associated with the current user
            $paniers = $panierRepository->findBy(['user' => $this->getUser()]);
        
            // Initialize total price
            $totalPrice = 0;
        
            // Add articles from paniers to the new Commande
            foreach ($paniers as $panier) {
                $article = $panier->getArticle();
                $commande->addArticle($article);
                $totalPrice += $article->getPrix(); // Assuming getPrix() returns the price of the article
            }
        
            // Check if sale code is entered and matches the predefined value
            $saleCode = $commande->getSaleCode();
            if ($saleCode && $saleCode === '20242024') {
                // Apply 20% discount
                $totalPrice *= 0.8;
            }
        
            // Set total price for the Commande
            $commande->setTotalPrice($totalPrice);
        
            // Associate the Commande with the current user
            $commande->setUser($this->getUser());
        
            // Persist and flush the Commande
            $entityManager->persist($commande);
            $entityManager->flush();
        
            // Clear the user's panier after creating the Commande
            foreach ($paniers as $panier) {
                $entityManager->remove($panier);
            }
            $entityManager->flush();
        
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'title' => 'Commande',
            'titlepage' => 'Commande',
            'controller_name' => 'CommandeController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
public function show(int $id, CommandeRepository $commandeRepository): Response
{
    // Fetch the Commande object by its ID
    $commande = $commandeRepository->find($id);

    // Check if a Commande object was found
    if (!$commande) {
        throw $this->createNotFoundException('Commande not found');
    }

    // Fetch the associated Paniers for the Commande
    $paniers = $commande->getPaniers();

    // Calculate the sum of panier prices
    $totalPrice = 0;
    foreach ($paniers as $panier) {
        $totalPrice += $panier->getPrixTot();
    }

    return $this->render('commande/show.html.twig', [
        'commande' => $commande,
        'paniers' => $paniers,
        'totalPrice' => $totalPrice,
    ]);
}

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            // Ensure the Commande belongs to the current user
            if ($commande->getUser() === $this->getUser()) {
                $entityManager->remove($commande);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/commande/{id}/pdf', name: 'commande_pdf')]
    public function generatePdf(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande not found');
        }
        // Render the template into HTML
        $html = $this->renderView('commande/pdf.html.twig', [
            'commande' => $commande,
        ]);

        // Create options for PDF generation
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        // Create the Dompdf instance
        $dompdf = new Dompdf($options);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Generate the PDF file content
        $pdfContent = $dompdf->output();

        // Return the PDF as a response
        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="commande_' . $commande->getId() . '.pdf"',
        ]);
    }
    
}
